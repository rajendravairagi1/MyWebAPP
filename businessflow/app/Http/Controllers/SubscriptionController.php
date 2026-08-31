<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Support\Tenant;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function expired(): View
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;

        return view('subscription-expired', [
            'business' => $business,
            'expiredOn' => $business?->effectiveExpiresAt(),
        ]);
    }
}
