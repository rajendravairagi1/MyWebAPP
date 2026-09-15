<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Called when the current user opens the notification bell while a
     * "plan expires in N day(s)" notice is showing — records that day
     * count so NotificationComposer stops counting it in the bell badge
     * until it changes (see the plan_expiry_seen_days migration). The
     * notice itself stays visible inside the dropdown either way; only
     * the badge stops re-nagging on every visit.
     */
    public function markPlanExpirySeen(Request $request): RedirectResponse
    {
        $days = (int) $request->validate(['days' => ['required', 'integer', 'min:0', 'max:7']])['days'];

        $request->user()->forceFill(['plan_expiry_seen_days' => $days])->save();

        return back();
    }
}
