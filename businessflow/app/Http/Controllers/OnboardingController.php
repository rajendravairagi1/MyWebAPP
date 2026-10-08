<?php

namespace App\Http\Controllers;

use App\Mail\TrialStartedAdminNotification;
use App\Models\PlatformSetting;
use App\Models\SignupRequest;
use App\Models\SubscriptionRenewal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        // Set only when this onboarding step was reached straight from
        // the auto-approved signup flow (see
        // Public\SignupRequestController::verify()) — every other way
        // of landing here behaves exactly as before: no plan, no
        // expiry, defaults to 'solo' with unlimited access.
        $signupRequest = null;
        if ($signupRequestId = $request->session()->get('trial_signup_request_id')) {
            $signupRequest = SignupRequest::where('status', 'pending')
                ->where('email', $request->user()->email)
                ->find($signupRequestId);
        }

        $business = $request->user()->businesses()->create($data + [
            'invoice_prefix' => 'INV',
            'plan' => $signupRequest->plan ?? 'solo',
            'phone' => $signupRequest->phone ?? null,
            'address' => $signupRequest->address ?? null,
            'subscription_expires_at' => $signupRequest ? now()->addDays(15) : null,
        ], [
            'role' => 'owner',
            'status' => 'active',
        ]);

        if ($signupRequest) {
            SubscriptionRenewal::create([
                'business_id' => $business->id,
                'source' => 'trial_auto',
                'plan' => $business->plan,
                'previous_expires_at' => null,
                'new_expires_at' => $business->subscription_expires_at,
                'note' => '15-day auto trial on signup',
            ]);

            $signupRequest->update(['status' => 'approved', 'business_id' => $business->id, 'reviewed_at' => now()]);

            // FYI only, not a gate — Platform Admin can still cut a
            // trial short by hand from the business list if one looks
            // wrong, same as any other account.
            Mail::to(config('platform.admin_email'))->send(new TrialStartedAdminNotification($business, $request->user()));

            $request->session()->forget('trial_signup_request_id');
        }

        $request->session()->put('active_business_id', $business->id);

        return redirect()->route('dashboard');
    }
}
