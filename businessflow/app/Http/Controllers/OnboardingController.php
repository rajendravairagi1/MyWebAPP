<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function create(): View
    {
        return view('onboarding.create', [
            'businessTypes' => config('business.types'),
            'currencies' => config('business.currencies'),
            'settings' => PlatformSetting::current(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:'.implode(',', array_keys(config('business.types')))],
            'country' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'timezone'],
        ]);

        $business = $request->user()->businesses()->create($data + [
            'invoice_prefix' => 'INV',
            // Reaching this form at all means the account has no
            // business yet (see IdentifyTenant) — today that only
            // happens right after self-signup, so this is the one place
            // the free trial actually starts. plan defaults to 'solo' at
            // the database level, matching the trial tier.
            'subscription_expires_at' => now()->addDays(7),
        ], [
            'role' => 'owner',
            'status' => 'active',
        ]);

        $request->session()->put('active_business_id', $business->id);

        return redirect()->route('dashboard');
    }
}
