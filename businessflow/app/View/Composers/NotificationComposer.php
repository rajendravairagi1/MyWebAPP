<?php

namespace App\View\Composers;

use App\Models\Branch;
use App\Models\Business;
use App\Models\Followup;
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

        $view->with([
            // Account-level (not tied to the active business) — drives the
            // "Company"/"My Branch" sidebar link and the "back up" link.
            'ownedCompany' => $user?->ownedCompany,
            'managedBranch' => $user && ! $user->ownedCompany ? $user->managedBranches()->first() : null,
            'activeBusinessBranch' => Tenant::check()
                ? Branch::with('company')->find(Business::find(Tenant::id())?->branch_id)
                : null,
        ]);

        if (! Tenant::check()) {
            $view->with([
                'dueFollowupsForBell' => collect(),
                'dueFollowupsCount' => 0,
                'dueCommitmentsForBell' => collect(),
                'dueCommitmentsCount' => 0,
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

        $view->with([
            'dueFollowupsForBell' => $due,
            'dueFollowupsCount' => Followup::where('status', 'pending')->where('due_at', '<=', now())->count(),
            'dueCommitmentsForBell' => $overdueCommitments,
            'dueCommitmentsCount' => ProjectUnit::whereNull('archived_at')->whereNotNull('commitment_date')->where('commitment_date', '<=', now()->toDateString())->count(),
        ]);
    }
}
