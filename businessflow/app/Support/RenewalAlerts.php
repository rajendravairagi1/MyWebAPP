<?php

namespace App\Support;

use App\Models\Business;
use App\Models\Company;
use Illuminate\Support\Collection;

/**
 * The platform admin's "who needs to renew" list, shared by the
 * notification bell (NotificationComposer) and the dedicated Expiring
 * Soon admin page — a standalone Business and every Company (a builder
 * under a branch has no billing of its own, so the Company row is what
 * to chase) due within 7 days OR already expired. Dismissed alerts stay
 * hidden until subscription_expires_at is changed again, which clears
 * renewal_alert_dismissed_at (see AdminController::updateExpiry()).
 */
class RenewalAlerts
{
    public static function all(): Collection
    {
        $cutoff = now()->addDays(7)->endOfDay();

        $businesses = Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $cutoff)
            ->whereNull('renewal_alert_dismissed_at')
            ->orderBy('subscription_expires_at')
            ->get()
            ->map(fn (Business $b) => [
                'type' => 'business',
                'id' => $b->id,
                'name' => $b->name,
                'plan' => $b->plan,
                'expires_at' => $b->subscription_expires_at,
                'expired' => $b->isSubscriptionExpired(),
            ]);

        $companies = Company::whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $cutoff)
            ->whereNull('renewal_alert_dismissed_at')
            ->orderBy('subscription_expires_at')
            ->get()
            ->map(fn (Company $c) => [
                'type' => 'company',
                'id' => $c->id,
                'name' => $c->name.' ('.__('Company').')',
                'plan' => 'company',
                'expires_at' => $c->subscription_expires_at,
                'expired' => $c->subscription_expires_at->copy()->endOfDay()->isPast(),
            ]);

        return $businesses->concat($companies)->sortBy('expires_at')->values();
    }
}
