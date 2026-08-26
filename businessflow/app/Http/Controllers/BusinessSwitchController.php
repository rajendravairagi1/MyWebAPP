<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BusinessSwitchController extends Controller
{
    /**
     * Enters a specific builder's workspace from the Branch/Company
     * dashboard — same session key onboarding sets on first signup, so
     * everything downstream (IdentifyTenant, module gating) just works.
     */
    public function switch(Request $request, Business $business): RedirectResponse
    {
        abort_unless($request->user()->businesses()->where('businesses.id', $business->id)->exists(), 403);

        $request->session()->put('active_business_id', $business->id);

        return redirect()->route('dashboard');
    }
}
