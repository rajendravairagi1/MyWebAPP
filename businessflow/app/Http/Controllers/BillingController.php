<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PlatformSetting;
use App\Support\DocumentQr;
use App\Support\GooglePlayBillingService;
use App\Support\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The "Renew Your Plan" page linked from the bell's plan-expiry notice
 * and from subscription-expired.blade.php — also shown (informationally,
 * nothing is blocked on it) on the signup/onboarding page, so a new
 * business can pay right away without waiting for a subscription to
 * actually expire first. Payment itself is collected manually outside
 * the app (scan/tap the platform's own UPI, pay, then the platform admin
 * updates the business's validity date by hand from the Admin panel) -
 * see App\Http\Middleware\EnsureSubscriptionActive's comment.
 */
class BillingController extends Controller
{
    public function show(): View
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;

        return view('billing.show', [
            'business' => $business,
            'expiresOn' => $business?->effectiveExpiresAt(),
            'settings' => PlatformSetting::current(),
            // Product ID -> plan, flipped to plan -> product ID for the
            // view — see resources/js/play-billing.js, which only shows
            // this section at all once it's confirmed (client-side) that
            // this page is running inside the Android app, not a browser.
            'googlePlayProducts' => array_flip(config('services.google_play.product_plan_map', [])),
        ]);
    }

    /**
     * Called by resources/js/play-billing.js once the Play Billing sheet
     * (triggered from inside the TWA app) hands back a purchase token —
     * never trusts that token's own claimed state, just hands it to
     * GooglePlayBillingService to look up fresh from Google and apply.
     */
    public function activateGooglePlay(Request $request, GooglePlayBillingService $billing): JsonResponse
    {
        $data = $request->validate(['purchase_token' => ['required', 'string']]);

        $business = Tenant::check() ? Business::find(Tenant::id()) : null;
        abort_unless($business, 401);

        // TEMPORARY — a real purchase completed on Google's side but this
        // call came back a plain 500 (production hides the real exception
        // behind a generic HTML error page, useless for diagnosing from a
        // phone with no log access). Catch it here and hand the message
        // back as JSON instead, so the client's debug alert shows the
        // actual failure. Revert to letting it propagate normally once
        // diagnosed.
        try {
            $purchase = $billing->activateFromToken($data['purchase_token'], $business);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'debug_exception' => get_class($e).': '.$e->getMessage(),
            ], 500);
        }

        abort_unless($purchase, 422, 'Could not verify this purchase with Google Play.');

        // TEMPORARY — the client got "ok" back but the business's plan/
        // expiry never changed. Echo exactly what Google reported and
        // what grantsAccess() decided, so the next screenshot shows why
        // the activation was skipped instead of guessing blind. Revert
        // once diagnosed.
        return response()->json([
            'status' => 'ok',
            'debug_purchase_status' => $purchase->status,
            'debug_expiry_time' => optional($purchase->expiry_time)->toIso8601String(),
            'debug_grants_access' => $purchase->grantsAccess(),
            'debug_business_plan' => $business->fresh()->plan,
            'debug_business_expires_at' => optional($business->fresh()->subscription_expires_at)->toDateString(),
        ]);
    }

    /**
     * The UPI ID is the source of truth once set — this generates the QR
     * fresh from it on every request, so it can never fall out of sync
     * the way a manually re-uploaded image could. Only falls back to an
     * old uploaded file for an install that set one before UPI ID
     * existed and hasn't added one yet.
     */
    public function paymentQr(): Response|BinaryFileResponse
    {
        $settings = PlatformSetting::current();

        if ($link = $settings->upiPaymentLink()) {
            $png = DocumentQr::png($link, size: 400);
            abort_unless($png, 404);

            return response($png, 200, ['Content-Type' => 'image/png']);
        }

        $path = $settings->payment_qr_path;
        abort_unless($path, 404);

        $absolute = Storage::disk('local')->path($path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute);
    }
}
