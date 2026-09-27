<?php

namespace App\Http\Controllers;

use App\Support\GooglePlayBillingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Google Play's Real-time Developer Notifications — a Pub/Sub push
 * subscription calls this the moment a subscription renews, cancels,
 * enters its grace period, gets refunded, and so on, so
 * Business::plan/subscription_expires_at stay correct without anyone
 * needing to reopen the app for it. See PLAY_BILLING_SETUP.md step 7 for
 * how the Pub/Sub topic/subscription pointing here gets set up.
 *
 * The notification itself only ever carries a bare purchase token — it's
 * GooglePlayBillingService::activateFromToken() that re-queries Google
 * for the subscription's actual current state and does the real work;
 * this controller is just the authenticated door into that.
 */
class GooglePlayNotificationController extends Controller
{
    public function __invoke(Request $request, GooglePlayBillingService $billing): Response
    {
        $jwt = str($request->header('Authorization', ''))->after('Bearer ')->trim()->toString();

        // Always 200 back to Google even when this is rejected/ignored —
        // Pub/Sub retries (with backoff, eventually giving up) on
        // anything other than 2xx, and a malformed or unauthenticated
        // request is never going to succeed on a later attempt either.
        if (! $jwt || ! $billing->verifyPubSubJwt($jwt)) {
            report('Google Play RTDN: rejected an unverifiable push request.');

            return response('', 204);
        }

        $envelope = $request->json('message', []);
        $payload = json_decode(base64_decode($envelope['data'] ?? ''), true);

        $purchaseToken = $payload['subscriptionNotification']['purchaseToken'] ?? null;
        $packageName = $payload['packageName'] ?? null;

        if (! $purchaseToken || $packageName !== config('services.google_play.package_name')) {
            return response('', 204);
        }

        $billing->activateFromToken($purchaseToken);

        return response('', 204);
    }
}
