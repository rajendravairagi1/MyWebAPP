<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PlatformSetting;
use App\Support\DocumentQr;
use App\Support\Tenant;
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
