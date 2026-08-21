<?php

namespace App\View\Composers;

use App\Models\Followup;
use App\Support\Tenant;
use Illuminate\View\View;

/**
 * Feeds the notification bell in layouts.app, which every authenticated
 * page renders — due/overdue follow-ups the current business owner
 * should know about. Kept deliberately cheap since it runs on every
 * request; Tenant::check() short-circuits it before login/onboarding.
 */
class NotificationComposer
{
    public function compose(View $view): void
    {
        if (! Tenant::check()) {
            $view->with(['dueFollowupsForBell' => collect(), 'dueFollowupsCount' => 0]);

            return;
        }

        $due = Followup::with('customer')
            ->where('status', 'pending')
            ->where('due_at', '<=', now())
            ->orderBy('due_at')
            ->limit(8)
            ->get();

        $view->with([
            'dueFollowupsForBell' => $due,
            'dueFollowupsCount' => Followup::where('status', 'pending')->where('due_at', '<=', now())->count(),
        ]);
    }
}
