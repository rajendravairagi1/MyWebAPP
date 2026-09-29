<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\SignupRequest;
use App\Support\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Account & Billing" — read-only, linked from the avatar menu next to
 * Business Settings. Shows the plan/expiry a customer already sees
 * elsewhere (the bell's renewal notice, billing.show) alongside the
 * history of how they got there, which nothing else surfaces: every
 * manual UPI renewal an admin recorded and every Google Play purchase,
 * oldest activity last. A Company-plan owner bills at the Company level
 * (see App\Models\Company), not per-business, so this resolves to
 * whichever of the two this user actually pays through.
 */
class AccountBillingController extends Controller
{
    public function show(Request $request): View
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;

        $company = $request->user()->ownedCompany ?? $business?->branch?->company;

        if ($company) {
            return view('account-billing.show', [
                'entityName' => $company->name,
                'planLabel' => 'Company',
                'expiresOn' => $company->subscription_expires_at,
                'isExpired' => $company->subscription_expires_at?->copy()->endOfDay()->isPast() ?? false,
                'renewals' => $company->subscriptionRenewals()->latest()->get(),
            ]);
        }

        abort_unless($business, 404);

        return view('account-billing.show', [
            'entityName' => $business->name,
            'planLabel' => SignupRequest::PLAN_LABELS[$business->plan] ?? ucfirst($business->plan),
            'expiresOn' => $business->subscription_expires_at,
            'isExpired' => $business->isSubscriptionExpired(),
            'renewals' => $business->subscriptionRenewals()->latest()->get(),
        ]);
    }
}
