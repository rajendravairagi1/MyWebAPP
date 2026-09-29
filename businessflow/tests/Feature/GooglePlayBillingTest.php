<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\PlayPurchase;
use App\Support\GooglePlayBillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GooglePlayBillingTest extends TestCase
{
    use RefreshDatabase;

    private string $rsaPrivateKey;

    private array $rsaKeyDetails;

    protected function setUp(): void
    {
        parent::setUp();

        $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($resource, $privateKey);
        $this->rsaPrivateKey = $privateKey;
        $this->rsaKeyDetails = openssl_pkey_get_details($resource);

        $keyPath = storage_path('app/test-google-play-key.json');
        file_put_contents($keyPath, json_encode([
            'client_email' => 'test@test.iam.gserviceaccount.com',
            'private_key' => $this->rsaPrivateKey,
        ]));

        config([
            'services.google_play.service_account_path' => $keyPath,
            'services.google_play.package_name' => 'com.probuildercrm.app',
            'services.google_play.product_plan_map' => ['probuildercrm_team' => 'team'],
            'services.google_play.rtdn_audience' => 'https://app.probuildercrm.com/api/webhooks/google-play-rtdn',
        ]);
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('app/test-google-play-key.json'));
        parent::tearDown();
    }

    private function fakeSubscriptionResponse(string $productId = 'probuildercrm_team', string $state = 'SUBSCRIPTION_STATE_ACTIVE', ?string $obfuscatedAccountId = null, ?string $expiryTime = null): array
    {
        $response = [
            'subscriptionState' => $state,
            'latestOrderId' => 'GPA.1234-5678',
            'acknowledgementState' => 'ACKNOWLEDGEMENT_STATE_PENDING',
            'lineItems' => [
                // Google always sends this as UTC ("Z"), never with this
                // app's own +05:30 offset — matching that exactly here is
                // what makes test_expiry_time_from_google_utc_z_timestamp_
                // is_correctly_interpreted_as_future() below able to catch
                // the timezone bug it's named for.
                ['productId' => $productId, 'expiryTime' => $expiryTime ?? now()->utc()->addDays(30)->format('Y-m-d\TH:i:s.v\Z')],
            ],
        ];

        if ($obfuscatedAccountId) {
            $response['externalAccountIdentifiers'] = ['obfuscatedExternalAccountId' => $obfuscatedAccountId];
        }

        return $response;
    }

    public function test_activate_from_purchase_updates_business_plan_and_expiry(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-token'], 200),
            'androidpublisher.googleapis.com/*' => Http::sequence()
                ->push($this->fakeSubscriptionResponse())
                ->push([]),
        ]);

        $business = Business::create(['name' => 'Test Biz', 'plan' => 'solo']);

        $purchase = app(GooglePlayBillingService::class)->activateFromToken('token-abc', $business);

        $business->refresh();
        $this->assertSame('team', $business->plan);
        $this->assertNotNull($business->subscription_expires_at);
        $this->assertSame('active', $purchase->status);
        $this->assertNotNull($purchase->acknowledged_at);
    }

    /**
     * Regression test for a real bug: Eloquent's date-format guessing
     * didn't recognise Google's UTC "...Z" shape and silently kept the
     * UTC clock digits while relabelling them as this app's Asia/Kolkata
     * timezone — no actual 5:30 shift applied. A purchase genuinely
     * expiring 3 hours from now (in real UTC time) would misread as
     * roughly 2.5 hours in the past, so grantsAccess() and the business's
     * subscription_expires_at would silently fail even for a perfectly
     * valid, currently-active purchase.
     */
    public function test_expiry_time_from_google_utc_z_timestamp_is_correctly_interpreted_as_future(): void
    {
        $utcExpiry = now()->utc()->addHours(3)->format('Y-m-d\TH:i:s.v\Z');

        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-token'], 200),
            'androidpublisher.googleapis.com/*' => Http::sequence()
                ->push($this->fakeSubscriptionResponse(expiryTime: $utcExpiry))
                ->push([]),
        ]);

        $business = Business::create(['name' => 'Test Biz', 'plan' => 'solo']);

        $purchase = app(GooglePlayBillingService::class)->activateFromToken('token-utc-z', $business);

        $this->assertTrue($purchase->expiry_time->isFuture(), 'A purchase expiring 3 real hours from now must not read as already past.');
        $this->assertTrue($purchase->grantsAccess());

        $business->refresh();
        $this->assertSame('team', $business->plan);
        $this->assertNotNull($business->subscription_expires_at);
    }

    public function test_activate_from_token_resolves_business_from_obfuscated_id_when_none_given(): void
    {
        $business = Business::create(['name' => 'Test Biz', 'plan' => 'solo']);

        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-token'], 200),
            'androidpublisher.googleapis.com/*' => Http::sequence()
                ->push($this->fakeSubscriptionResponse(obfuscatedAccountId: (string) $business->id))
                ->push([]),
        ]);

        $purchase = app(GooglePlayBillingService::class)->activateFromToken('token-xyz');

        $this->assertNotNull($purchase);
        $this->assertSame($business->id, $purchase->business_id);
    }

    public function test_activate_from_token_gives_up_quietly_with_no_way_to_attribute_business(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-token'], 200),
            'androidpublisher.googleapis.com/*' => Http::response($this->fakeSubscriptionResponse()),
        ]);

        $purchase = app(GooglePlayBillingService::class)->activateFromToken('orphan-token');

        $this->assertNull($purchase);
        $this->assertSame(0, PlayPurchase::count());
    }

    public function test_rtdn_webhook_rejects_an_unsigned_request(): void
    {
        $response = $this->postJson('/api/webhooks/google-play-rtdn', [
            'message' => ['data' => base64_encode(json_encode(['packageName' => 'com.probuildercrm.app']))],
        ]);

        $response->assertStatus(204);
        $this->assertSame(0, PlayPurchase::count());
    }

    public function test_rtdn_webhook_activates_a_purchase_from_a_properly_signed_push(): void
    {
        $business = Business::create(['name' => 'Test Biz', 'plan' => 'solo']);

        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-token'], 200),
            'www.googleapis.com/oauth2/v1/certs' => Http::response(['test-kid' => $this->selfSignedCertPem()]),
            'androidpublisher.googleapis.com/*' => Http::sequence()
                ->push($this->fakeSubscriptionResponse(obfuscatedAccountId: (string) $business->id))
                ->push([]),
        ]);

        $jwt = $this->signedGoogleJwt([
            'iss' => 'https://accounts.google.com',
            'aud' => 'https://app.probuildercrm.com/api/webhooks/google-play-rtdn',
            'exp' => now()->addMinutes(5)->timestamp,
        ]);

        $payload = [
            'packageName' => 'com.probuildercrm.app',
            'subscriptionNotification' => [
                'notificationType' => 2,
                'purchaseToken' => 'signed-push-token',
            ],
        ];

        $response = $this->postJson('/api/webhooks/google-play-rtdn', [
            'message' => ['data' => base64_encode(json_encode($payload))],
        ], ['Authorization' => "Bearer {$jwt}"]);

        $response->assertStatus(204);
        $this->assertDatabaseHas('play_purchases', [
            'business_id' => $business->id,
            'purchase_token' => 'signed-push-token',
        ]);
    }

    /** A self-signed X.509 cert wrapping the same key pair the test JWTs are signed with — standing in for Google's own /oauth2/v1/certs response. */
    private function selfSignedCertPem(): string
    {
        $resource = openssl_pkey_get_private($this->rsaPrivateKey);
        $csr = openssl_csr_new(['commonName' => 'test'], $resource);
        $cert = openssl_csr_sign($csr, null, $resource, 365);
        openssl_x509_export($cert, $pem);

        return $pem;
    }

    private function signedGoogleJwt(array $claims): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT', 'kid' => 'test-kid'];
        $encode = fn ($data) => rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');

        $signingInput = $encode($header).'.'.$encode($claims);
        openssl_sign($signingInput, $signature, $this->rsaPrivateKey, OPENSSL_ALGO_SHA256);
        $signatureB64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "{$signingInput}.{$signatureB64}";
    }
}
