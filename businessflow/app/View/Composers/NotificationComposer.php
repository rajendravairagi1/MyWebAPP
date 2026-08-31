<?php

namespace App\View\Composers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\Company;
use App\Models\Followup;
use App\Models\Meeting;
use App\Models\ProjectUnit;
use App\Support\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Feeds the notification bell in layouts.app, which every authenticated
 * page renders — due/overdue follow-ups and overdue possession
 * commitments the current business owner should know about. Kept
 * deliberately cheap since it runs on every request; Tenant::check()
 * short-circuits it before login/onboarding.
 */
class NotificationComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();
        $isPlatformAdmin = $user && $user->email === config('platform.admin_email');
        $activeBusiness = Tenant::check() ? Business::find(Tenant::id()) : null;

        $view->with([
            // Account-level (not tied to the active business) — drives the
            // "Company"/"My Branch" sidebar link and the "back up" link.
            'ownedCompany' => $user?->ownedCompany,
            'managedBranch' => $user && ! $user->ownedCompany ? $user->managedBranches()->first() : null,
            'activeBusinessBranch' => $activeBusiness ? Branch::with('company')->find($activeBusiness->branch_id) : null,
            'canCreateCompany' => $user && ! $user->ownedCompany && $user->hasCompanyPlan(),
            'isPlatformAdmin' => $isPlatformAdmin,
        ]);

        // Nudge toward renewal starting 7 days out — never for the platform
        // admin themselves, and never once it's actually expired (they're
        // already redirected to the "subscription expired" page by then,
        // see App\Http\Middleware\EnsureSubscriptionActive).
        $expiresOn = ! $isPlatformAdmin ? $activeBusiness?->effectiveExpiresAt() : null;
        $daysRemaining = $expiresOn ? now()->startOfDay()->diffInDays($expiresOn->copy()->startOfDay(), false) : null;

        $view->with([
            'subscriptionExpiresOn' => ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7) ? $expiresOn : null,
            'subscriptionDaysRemaining' => ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7) ? $daysRemaining : null,
        ]);

        // The platform admin's own "who needs to renew" list — every
        // standalone Business and every Company (a builder under a branch
        // has no billing of its own, so the Company row is what to chase)
        // due within 7 days OR already expired, so admin sees this the
        // moment they log in rather than only after opening /admin.
        $view->with($isPlatformAdmin ? $this->adminRenewalAlerts() : [
            'adminRenewalAlerts' => collect(),
            'adminRenewalCount' => 0,
        ]);

        if (! Tenant::check()) {
            $view->with([
                'dueFollowupsForBell' => collect(),
                'dueFollowupsCount' => 0,
                'dueCommitmentsForBell' => collect(),
                'dueCommitmentsCount' => 0,
                'dueMeetingsForBell' => collect(),
                'dueMeetingsCount' => 0,
            ]);

            return;
        }

        $due = Followup::with('customer')
            ->where('status', 'pending')
            ->where('due_at', '<=', now())
            ->orderBy('due_at')
            ->limit(8)
            ->get();

        $overdueCommitments = ProjectUnit::with(['project', 'customer'])
            ->whereNull('archived_at')
            ->whereNotNull('commitment_date')
            ->where('commitment_date', '<=', now()->toDateString())
            ->orderBy('commitment_date')
            ->limit(8)
            ->get();

        // Upcoming-within-a-day (not just overdue) — a meeting reminder is
        // more useful shown ahead of time than only after it's been missed.
        $dueMeetings = Meeting::with('customer')
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now()->addDay())
            ->orderBy('scheduled_at')
            ->limit(8)
            ->get();

        $view->with([
            'dueFollowupsForBell' => $due,
            'dueFollowupsCount' => Followup::where('status', 'pending')->where('due_at', '<=', now())->count(),
            'dueCommitmentsForBell' => $overdueCommitments,
            'dueCommitmentsCount' => ProjectUnit::whereNull('archived_at')->whereNotNull('commitment_date')->where('commitment_date', '<=', now()->toDateString())->count(),
            'dueMeetingsForBell' => $dueMeetings,
            'dueMeetingsCount' => Meeting::where('status', 'scheduled')->where('scheduled_at', '<=', now()->addDay())->count(),
        ]);
    }

    /**
     * @return array{adminRenewalAlerts: \Illuminate\Support\Collection, adminRenewalCount: int}
     */
    private function adminRenewalAlerts(): array
    {
        $cutoff = now()->addDays(7)->endOfDay();

        $businesses = Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $cutoff)
            ->orderBy('subscription_expires_at')
            ->get()
            ->map(fn (Business $b) => [
                'name' => $b->name,
                'expires_at' => $b->subscription_expires_at,
                'expired' => $b->isSubscriptionExpired(),
            ]);

        $companies = Company::whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $cutoff)
            ->orderBy('subscription_expires_at')
            ->get()
            ->map(fn (Company $c) => [
                'name' => $c->name.' ('.__('Company').')',
                'expires_at' => $c->subscription_expires_at,
                'expired' => $c->subscription_expires_at->copy()->endOfDay()->isPast(),
            ]);

        $alerts = $businesses->concat($companies)->sortBy('expires_at')->take(10)->values();

        return [
            'adminRenewalAlerts' => $alerts,
            'adminRenewalCount' => $alerts->count(),
        ];
    }
}
