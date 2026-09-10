<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Support\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function expired(Request $request): View
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;

        return view('subscription-expired', [
            'business' => $business,
            'expiredOn' => $business?->effectiveExpiresAt(),
            // Distinguishes "past its paid-through date" from "switched
            // off from the Platform Admin panel" — see
            // EnsureSubscriptionActive, which is the only place this is set.
            'paused' => $request->query('reason') === 'paused',
        ]);
    }
}
