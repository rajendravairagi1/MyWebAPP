<?php

namespace App\View\Composers;

use App\Models\AppNotification;
use App\Models\Branch;
use App\Models\Business;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\ProjectUnit;
use App\Models\SignupRequest;
use App\Support\RenewalAlerts;
use App\Support\Tenant;
use Illuminate\Support\Collection;
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

        $activeBusinessBranch = $activeBusiness ? Branch::with('company')->find($activeBusiness->branch_id) : null;
        $ownedCompany = $user?->ownedCompany;

        $view->with([
            // Shared so the footer can gate its WhatsApp Support link to
            // paying customers only — never the public demo account, and
            // never when there's no active business at all (e.g. the
            // platform admin's own panel).
            'activeBusiness' => $activeBusiness,
            // Account-level (not tied to the active business) — drives the
            // "Company"/"My Branch" sidebar link and the "back up" link.
            'ownedCompany' => $ownedCompany,
            'managedBranch' => $user && ! $ownedCompany ? $user->managedBranches()->first() : null,
            'activeBusinessBranch' => $activeBusinessBranch,
            'activeBusinessName' => $activeBusiness?->name,
            // Every branch under this user's own Company — feeds the header
            // "Branches" quick-switch dropdown so a Company Owner can jump
            // straight into any one branch's own dashboard from anywhere,
            // rather than going through the company-wide summary first.
            'ownedCompanyBranches' => $ownedCompany ? $ownedCompany->branches()->orderBy('name')->get() : collect(),
            'canCreateCompany' => $user && ! $ownedCompany && $user->hasCompanyPlan(),
            'isPlatformAdmin' => $isPlatformAdmin,
        ]);

        // Nudge toward renewal starting 7 days out — never for the platform
        // admin themselves, and never once it's actually expired (they're
        // already redirected to the "subscription expired" page by then,
        // see App\Http\Middleware\EnsureSubscriptionActive).
        $expiresOn = ! $isPlatformAdmin ? $activeBusiness?->effectiveExpiresAt() : null;
        $daysRemaining = $expiresOn ? now()->startOfDay()->diffInDays($expiresOn->copy()->startOfDay(), false) : null;

        $view->with([
            // Stays visible in the bell dropdown (with a "Pay Now" button)
            // for the whole 7-day window right up until renewed - but
            // deliberately never counted into $bellCount in layouts.app,
            // since it isn't a discrete new event the way a lead or
            // follow-up is, and shouldn't be able to bury one under a
            // badge number that never goes away on its own.
            'subscriptionExpiresOn' => ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7) ? $expiresOn : null,
            'subscriptionDaysRemaining' => ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7) ? $daysRemaining : null,
        ]);

        // The platform admin's own "who needs to renew" list — every
        // standalone Business and every Company (a builder under a branch
        // has no billing of its own, so the Company row is what to chase)
        // due within 7 days OR already expired, so admin sees this the
        // moment they log in rather than only after opening /admin.
        $view->with($isPlatformAdmin ? $this->adminRenewalAlertsForBell() : [
            'adminRenewalAlerts' => collect(),
            'adminRenewalCount' => 0,
        ]);

        // Badge for the "Signup Requests" nav item, and the bell dropdown
        // list below — how many public /get-started submissions are
        // still waiting on Approve/Reject.
        $pendingSignupRequests = $isPlatformAdmin
            ? SignupRequest::where('status', 'pending')->latest()->limit(8)->get()
            : collect();

        $view->with([
            'pendingSignupRequestsForBell' => $pendingSignupRequests,
            'pendingSignupRequestsCount' => $isPlatformAdmin ? SignupRequest::where('status', 'pending')->count() : 0,
        ]);

        if (! Tenant::check()) {
            $view->with([
                'notificationItems' => collect(),
                'dueFollowupsCount' => 0,
                'pendingLeadsCount' => 0,
                'dueCommitmentsCount' => 0,
                'dueMeetingsCount' => 0,
            ]);

            return;
        }

        $due = Followup::with(['customer', 'lead'])
            ->where('status', 'pending')
            ->where('due_at', '<=', now())
            ->orderBy('due_at')
            ->limit(8)
            ->get();

        $pendingLeads = Lead::where('status', Lead::STATUS_PENDING)
            ->latest()
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

        $candidates = collect()
            ->concat($due->map(fn (Followup $f) => [
                'type' => 'followup',
                'source_id' => $f->id,
                'title' => $f->contact()?->name ?? __('Follow-up'),
                'body' => $f->note,
                'url' => $f->customer_id ? route('customers.show', $f->customer_id) : route('leads.show', $f->lead_id),
            ]))
            ->concat($pendingLeads->map(fn (Lead $l) => [
                'type' => 'lead',
                'source_id' => $l->id,
                'title' => $l->name,
                'body' => __('New lead awaiting approval'),
                'url' => route('leads.show', $l),
            ]))
            ->concat($overdueCommitments->map(fn (ProjectUnit $u) => [
                'type' => 'commitment',
                'source_id' => $u->id,
                'title' => $u->customer?->name ?? __('Possession commitment'),
                'body' => $u->project->name.' · '.$u->unit_number,
                'url' => $u->customer ? route('customers.show', $u->customer) : route('projects.show', $u->project),
            ]))
            ->concat($dueMeetings->map(fn (Meeting $m) => [
                'type' => 'meeting',
                'source_id' => $m->id,
                'title' => $m->title,
                'body' => $m->customer?->name,
                'url' => route('meetings.index'),
            ]));

        $notificationItems = $this->syncNotifications($candidates)
            ->reject(fn (AppNotification $n) => $n->dismissed_at)
            ->sortByDesc('created_at')
            ->values();

        $view->with([
            'notificationItems' => $notificationItems,
            'dueFollowupsCount' => Followup::where('status', 'pending')->where('due_at', '<=', now())->count(),
            'pendingLeadsCount' => Lead::where('status', Lead::STATUS_PENDING)->count(),
            'dueCommitmentsCount' => ProjectUnit::whereNull('archived_at')->whereNotNull('commitment_date')->where('commitment_date', '<=', now()->toDateString())->count(),
            'dueMeetingsCount' => Meeting::where('status', 'scheduled')->where('scheduled_at', '<=', now()->addDay())->count(),
        ]);
    }

    /**
     * Idempotently turns every "currently due" item into a persisted
     * AppNotification row, in two queries total (not one per item) —
     * this runs on every authenticated page load, so N+1 here would mean
     * N+1 extra queries on every single request. Rows that already exist
     * are left untouched (never resets a dismissed_at the user already
     * set), only genuinely new ones get inserted.
     *
     * @param  Collection<int, array{type: string, source_id: int, title: ?string, body: ?string, url: string}>  $candidates
     * @return Collection<int, AppNotification>
     */
    private function syncNotifications(Collection $candidates): Collection
    {
        if ($candidates->isEmpty()) {
            return collect();
        }

        $businessId = Tenant::id();

        $existing = AppNotification::where('business_id', $businessId)
            ->where(function ($query) use ($candidates) {
                foreach ($candidates->groupBy('type') as $type => $items) {
                    $query->orWhere(fn ($q) => $q->where('type', $type)->whereIn('source_id', $items->pluck('source_id')));
                }
            })
            ->get()
            ->keyBy(fn (AppNotification $n) => $n->type.':'.$n->source_id);

        $toInsert = $candidates
            ->reject(fn (array $c) => $existing->has($c['type'].':'.$c['source_id']))
            ->map(fn (array $c) => [
                'business_id' => $businessId,
                'type' => $c['type'],
                'source_id' => $c['source_id'],
                'title' => $c['title'] ?: ucfirst($c['type']),
                'body' => $c['body'],
                'url' => $c['url'],
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values();

        if ($toInsert->isNotEmpty()) {
            AppNotification::insert($toInsert->all());

            $existing = AppNotification::where('business_id', $businessId)
                ->where(function ($query) use ($candidates) {
                    foreach ($candidates->groupBy('type') as $type => $items) {
                        $query->orWhere(fn ($q) => $q->where('type', $type)->whereIn('source_id', $items->pluck('source_id')));
                    }
                })
                ->get()
                ->keyBy(fn (AppNotification $n) => $n->type.':'.$n->source_id);
        }

        return $candidates
            ->map(fn (array $c) => $existing->get($c['type'].':'.$c['source_id']))
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * @return array{adminRenewalAlerts: Collection, adminRenewalCount: int}
     */
    private function adminRenewalAlertsForBell(): array
    {
        $alerts = RenewalAlerts::all()->take(10)->values();

        return [
            'adminRenewalAlerts' => $alerts,
            'adminRenewalCount' => $alerts->count(),
        ];
    }
}
