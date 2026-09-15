<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PlatformSetting;
use App\Support\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The "Renew Your Plan" page linked from the bell's plan-expiry notice
 * and from subscription-expired.blade.php. Payment itself is collected
 * manually outside the app (scan the platform's own QR, pay, then the
 * platform admin updates the business's validity date by hand from the
 * Admin panel) - see App\Http\Middleware\EnsureSubscriptionActive's
 * comment. Reachable even once a subscription has actually expired
 * (billing.* is allow-listed there), since that's exactly when a
 * business needs this page most.
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

    public function paymentQr(): BinaryFileResponse
    {
        $path = PlatformSetting::current()->payment_qr_path;

        abort_unless($path, 404);

        $absolute = Storage::disk('local')->path($path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute);
    }
}
