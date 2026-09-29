<?php

namespace App\Support;

use App\Models\Business;
use App\Models\PlayPurchase;
use App\Models\SubscriptionRenewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to Google's Android Publisher API (Play Developer API v3) as the
 * service account set up in Play Console — no google/apiclient dependency
 * (that package pulls in the entire Google API surface for one endpoint,
 * and installing it here timed out against this environment's restricted
 * network anyway). The whole auth flow is one signed JWT exchanged for an
 * access token — openssl (already required by Laravel itself) is all
 * that's needed.
 *
 * See PLAY_BILLING_SETUP.md for the one-time Play Console / Google Cloud
 * steps this depends on: a service account with Android Publisher API
 * access, linked to this app in Play Console, its JSON key saved at
 * config('services.google_play.service_account_path').
 */
class GooglePlayBillingService
{
    private const TOKEN_URI = 'https://oauth2.googleapis.com/token';

    private const SCOPE = 'https://www.googleapis.com/auth/androidpublisher';

    private const API_BASE = 'https://androidpublisher.googleapis.com/androidpublisher/v3/applications';

    private const GOOGLE_CERTS_URI = 'https://www.googleapis.com/oauth2/v1/certs';

    /**
     * A short-lived Google OAuth2 access token, cached just under its own
     * 1-hour lifetime so a burst of verification calls (e.g. a Real-time
     * Developer Notification arriving right after a purchase) doesn't
     * re-sign and re-exchange a fresh JWT for every single one.
     */
    public function accessToken(): string
    {
        return Cache::remember('google_play_billing_access_token', now()->addMinutes(50), function () {
            $credentials = $this->serviceAccountCredentials();
            $jwt = $this->signedJwt($credentials);

            $response = Http::asForm()->post(self::TOKEN_URI, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->ok()) {
                throw new RuntimeException('Google OAuth2 token exchange failed: '.$response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * The Play Developer API v2 subscription-purchase lookup — one call
     * tells us the plan's current state and paid-through date, which is
     * everything activateFromPurchase() below needs to decide what to do.
     */
    public function fetchSubscription(string $purchaseToken): array
    {
        $packageName = config('services.google_play.package_name');
        abort_if(! $packageName, 500, 'ANDROID_PACKAGE_NAME is not set.');

        $url = self::API_BASE."/{$packageName}/purchases/subscriptionsv2/tokens/{$purchaseToken}";

        $response = Http::withToken($this->accessToken())->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Google Play subscription lookup failed ({$response->status()}): {$response->body()}");
        }

        return $response->json();
    }

    /**
     * Google auto-refunds a subscription if its initial purchase is never
     * acknowledged within 3 days — this is that acknowledgement, and it's
     * only ever needed once per purchase token (see
     * PlayPurchase::acknowledged_at), never on a renewal.
     */
    public function acknowledge(string $productId, string $purchaseToken): void
    {
        $packageName = config('services.google_play.package_name');
        $url = self::API_BASE."/{$packageName}/purchases/subscriptions/{$productId}/tokens/{$purchaseToken}:acknowledge";

        $response = Http::withToken($this->accessToken())->post($url, ['developerPayload' => '']);

        // Google's own success response for this endpoint is 204 No
        // Content, not 200 — ok() checks for exactly 200, which made this
        // throw on every genuinely successful acknowledgement.
        // successful() correctly covers the whole 2xx range.
        if (! $response->successful()) {
            throw new RuntimeException("Google Play acknowledge failed ({$response->status()}) for url {$url}: {$response->body()}");
        }
    }

    /**
     * The single entry point both the billing page's "purchase complete"
     * callback and the RTDN webhook use — looks the purchase up fresh
     * from Google (never trusts a client-supplied status), then brings
     * PlayPurchase and Business::plan/subscription_expires_at in line
     * with whatever Google actually says right now.
     *
     * $business is known outright when the billing page's own "purchase
     * complete" callback calls this (a logged-in session already ties
     * the purchase to a business). The RTDN webhook has no such session —
     * it only ever gets a bare purchase token — so there $business is
     * left null and resolved below, in order: an existing PlayPurchase
     * row for this same token, or (first-ever notification for a brand
     * new purchase, arriving before the client-side callback does)
     * Google's own echo of the obfuscated account id the billing page
     * attached when the purchase was started — see resources/js for
     * where that gets set. A purchase Google reports with neither is one
     * this code genuinely cannot attribute to any business and is
     * skipped rather than guessed at.
     */
    public function activateFromToken(string $purchaseToken, ?Business $business = null): ?PlayPurchase
    {
        $data = $this->fetchSubscription($purchaseToken);

        $lineItem = $data['lineItems'][0] ?? null;
        abort_if(! $lineItem, 500, 'Google Play response had no line items.');

        $business ??= PlayPurchase::where('purchase_token', $purchaseToken)->first()?->business;
        $business ??= $this->resolveBusinessFromObfuscatedId($data);

        if (! $business) {
            report(new RuntimeException("Google Play purchase token {$purchaseToken} could not be attributed to any business."));

            return null;
        }

        $productId = $lineItem['productId'];
        // Google always sends this as UTC (the trailing "Z"). Handing the
        // raw string straight to Eloquent's date cast is what caused the
        // bug this replaces: its format-guessing didn't recognise this
        // exact shape and silently kept the UTC clock digits while
        // relabelling them as this app's Asia/Kolkata timezone — no
        // actual 5:30 shift — so a genuinely-future expiry read as
        // hours in the past. Parsing explicitly (respecting the "Z") and
        // converting to the app's own timezone before Eloquent ever sees
        // it sidesteps that guessing entirely, and also matches what
        // Eloquent assumes when it later formats this for storage as a
        // timezone-less DATETIME column.
        $expiryTime = Carbon::parse($lineItem['expiryTime'])->setTimezone(config('app.timezone'));
        $status = $this->mapSubscriptionState($data['subscriptionState'] ?? null);

        $purchase = PlayPurchase::updateOrCreate(
            ['purchase_token' => $purchaseToken],
            [
                'business_id' => $business->id,
                'product_id' => $productId,
                'order_id' => $data['latestOrderId'] ?? null,
                'status' => $status,
                'expiry_time' => $expiryTime,
                'last_api_response' => $data,
            ]
        );

        $needsAcknowledgement = ($data['acknowledgementState'] ?? null) === 'ACKNOWLEDGEMENT_STATE_PENDING';
        if ($needsAcknowledgement && ! $purchase->acknowledged_at) {
            $this->acknowledge($productId, $purchaseToken);
            $purchase->update(['acknowledged_at' => now()]);
        }

        if ($purchase->grantsAccess()) {
            $plan = config('services.google_play.product_plan_map')[$productId] ?? null;
            $previousExpiresAt = $business->subscription_expires_at;
            $newExpiresAt = $purchase->expiry_time->toDateString();

            $business->update([
                'plan' => $plan ?: $business->plan,
                'subscription_expires_at' => $newExpiresAt,
            ]);

            // Google re-sends the same renewal state on retries/duplicate
            // RTDN deliveries — only log it as a new history entry the
            // first time the expiry actually moves.
            if (! $previousExpiresAt?->toDateString() || $previousExpiresAt->toDateString() !== $newExpiresAt) {
                SubscriptionRenewal::create([
                    'business_id' => $business->id,
                    'source' => 'google_play',
                    'plan' => $plan ?: $business->plan,
                    'previous_expires_at' => $previousExpiresAt,
                    'new_expires_at' => $newExpiresAt,
                    'note' => $purchase->order_id,
                ]);
            }
        }

        return $purchase;
    }

    private function resolveBusinessFromObfuscatedId(array $data): ?Business
    {
        $id = $data['externalAccountIdentifiers']['obfuscatedExternalAccountId'] ?? null;

        return $id ? Business::find($id) : null;
    }

    /**
     * Confirms a Pub/Sub push request's "Authorization: Bearer <jwt>"
     * header really was signed by Google just now, for this app
     * specifically — without this, anyone who finds the webhook URL
     * could POST a fake "renewed!" notification and grant themselves a
     * subscription for free. Google's own docs describe exactly this
     * verification (https://cloud.google.com/pubsub/docs/push#validate_message_authenticity);
     * this is that same check, using nothing beyond openssl + Http
     * (already needed elsewhere in this class) rather than a JWT
     * library, consistent with the rest of this file.
     */
    public function verifyPubSubJwt(string $jwt): bool
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        [$headerB64, $claimsB64, $signatureB64] = $parts;

        $header = json_decode($this->base64UrlDecode($headerB64), true);
        $claims = json_decode($this->base64UrlDecode($claimsB64), true);
        $signature = $this->base64UrlDecode($signatureB64);

        if (! $header || ! $claims || ! isset($header['kid'])) {
            return false;
        }

        $cert = $this->googleCert($header['kid']);
        if (! $cert) {
            return false;
        }

        $publicKey = openssl_pkey_get_public($cert);
        if (! $publicKey) {
            return false;
        }

        $signingInput = "{$headerB64}.{$claimsB64}";
        $verified = openssl_verify($signingInput, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1;

        $issuerOk = in_array($claims['iss'] ?? null, ['https://accounts.google.com', 'accounts.google.com'], true);
        $audience = config('services.google_play.rtdn_audience');
        $audienceOk = $audience && ($claims['aud'] ?? null) === $audience;
        $notExpired = ($claims['exp'] ?? 0) > time();

        return $verified && $issuerOk && $audienceOk && $notExpired;
    }

    private function googleCert(string $kid): ?string
    {
        $certs = Cache::remember('google_pubsub_certs', now()->addHours(6), function () {
            $response = Http::get(self::GOOGLE_CERTS_URI);

            return $response->ok() ? $response->json() : [];
        });

        return $certs[$kid] ?? null;
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function mapSubscriptionState(?string $state): string
    {
        return match ($state) {
            'SUBSCRIPTION_STATE_ACTIVE' => 'active',
            'SUBSCRIPTION_STATE_CANCELED' => 'canceled',
            'SUBSCRIPTION_STATE_IN_GRACE_PERIOD' => 'in_grace_period',
            'SUBSCRIPTION_STATE_ON_HOLD' => 'on_hold',
            'SUBSCRIPTION_STATE_PAUSED' => 'paused',
            'SUBSCRIPTION_STATE_EXPIRED' => 'expired',
            'SUBSCRIPTION_STATE_REVOKED' => 'revoked',
            // SUBSCRIPTION_STATE_PENDING (a pending cash-based purchase
            // not yet completed) and anything Google adds in future all
            // land here — "on_hold" never grants access, which is the
            // only safe default for a state this code doesn't recognise.
            default => 'on_hold',
        };
    }

    private function serviceAccountCredentials(): array
    {
        $path = config('services.google_play.service_account_path');
        abort_if(! $path || ! file_exists($path), 500, 'Google Play service account JSON key not found at '.$path);

        $credentials = json_decode(file_get_contents($path), true);
        abort_if(! is_array($credentials) || empty($credentials['private_key']) || empty($credentials['client_email']), 500, 'Google Play service account JSON key is malformed.');

        return $credentials;
    }

    /**
     * The JWT Bearer flow Google's OAuth2 server accepts from a service
     * account with no user in the loop — signed with the service
     * account's own RSA private key, which only Google's token endpoint
     * ever needs to verify (it already has the matching public key from
     * when the service account was created).
     */
    private function signedJwt(array $credentials): string
    {
        $now = time();

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $credentials['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_URI,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($claims)),
        ];

        $signingInput = implode('.', $segments);

        $signature = '';
        $signed = openssl_sign($signingInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        abort_unless($signed, 500, 'Could not sign the Google Play service-account JWT.');

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
