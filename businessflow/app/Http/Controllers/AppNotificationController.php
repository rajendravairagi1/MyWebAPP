<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Support\ListPagination;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppNotificationController extends Controller
{
    /**
     * Every notification this business has ever had, dismissed or not —
     * the bell only ever shows what's still active; this is the full
     * history, kept until someone deletes a row.
     */
    public function index(Request $request): View
    {
        $notifications = AppNotification::orderByDesc('created_at')
            ->paginate(ListPagination::perPage($request));

        return view('notifications.index', compact('notifications'));
    }

    /**
     * "Done" from the bell — hides it there without deleting it, so the
     * full history stays on /notifications.
     */
    public function dismiss(AppNotification $notification): RedirectResponse
    {
        $notification->update(['dismissed_at' => now()]);

        return back()->with('status', 'Notification dismissed.');
    }

    public function destroy(AppNotification $notification): RedirectResponse
    {
        $notification->delete();

        return back()->with('status', 'Notification deleted.');
    }
}
