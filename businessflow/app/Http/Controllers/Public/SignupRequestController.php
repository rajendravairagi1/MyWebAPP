<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\SignupRequestVerificationMail;
use App\Models\SignupRequest;
use App\Models\User;
use App\Support\Recaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The public, no-login "I'd like an account" form a QR code (or a plain
 * shared link) on Platform Admin points a prospect at — the account-
 * request counterpart to the per-business Lead form. For a solo/team
 * signup, confirming the email now drops them straight into a 15-day
 * trial (see verify() below) instead of waiting on Platform Admin —
 * someone who has to wait for someone else to act on their request
 * mostly never comes back. A company-plan request still lands in the
 * admin queue (see Admin\SignupRequestAdminController) since those are
 * multi-branch and genuinely need setting up by hand.
 */
class SignupRequestController extends Controller
{
    /**
     * This is also the PWA manifest's start_url (see PwaController) — the
     * page the Android app opens to fresh, for someone with no session
     * yet. Someone who already has one and opens the app anyway (their
     * session outlives any one visit) shouldn't be shown a signup form
     * for an account they're already logged into.
     */
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('signup-requests.public-form');
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot — same pattern as the Lead public form: a real visitor
        // never sees or fills this field; a script filling every field
        // blind will. Pretend success rather than tipping it off.
        if (filled($request->input('hp_check_1'))) {
            return redirect()->route('signup-requests.public.show')->with('requestSubmitted', true);
        }

        // Invisible (v3, no checkbox) — scores this submit in the
        // background. Skipped entirely when RECAPTCHA_SECRET_KEY isn't
        // set (see config('services.recaptcha')), so this never blocks
        // signups on an unconfigured environment.
        if (! Recaptcha::passes($request->input('recaptcha_token'), 'signup')) {
            return back()->withErrors(['recaptcha' => 'Spam check failed — please reload the page and try again.'])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => [
                'required', 'email', 'max:255',
                'unique:users,email',
                Rule::unique('signup_requests', 'email')->where(fn ($q) => $q->where('status', 'pending')),
            ],
            'password' => ['required', 'string', 'min:8'],
            'plan' => ['required', 'in:solo,team,company'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'email.unique' => 'An account (or a request awaiting approval) already uses this email.',
        ]);

        $signupRequest = SignupRequest::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password_hash' => bcrypt($data['password']),
            'plan' => $data['plan'],
            'address' => $data['address'] ?? null,
            // Not 'pending' yet — see verify() below. Admin's queue only
            // ever shows a request once its email is confirmed real, so
            // a mistyped or made-up address never reaches him at all.
            'status' => 'unverified',
        ]);

        Mail::to($signupRequest->email)->send(new SignupRequestVerificationMail($signupRequest));

        return redirect()->route('signup-requests.public.show')->with('requestSubmitted', true);
    }

    /**
     * The link sent by SignupRequestVerificationMail. Confirming here is
     * what moves the request into Admin's 'pending' queue — see
     * SignupRequest::isVerified() and Admin\SignupRequestAdminController
     * — for a company-plan request. For solo/team, it instead creates
     * the login right here and sends them on to the one-time business-
     * details step (see OnboardingController::store, which is what
     * actually starts the 15-day trial and flips this request to
     * 'approved' once that step is done).
     */
    public function verify(Request $request, SignupRequest $signupRequest): View|RedirectResponse
    {
        abort_unless(hash_equals(sha1($signupRequest->email), (string) $request->route('hash')), 403);

        if (! $signupRequest->isVerified() && $signupRequest->status === 'unverified') {
            $signupRequest->update(['status' => 'pending', 'email_verified_at' => now()]);

            // A duplicate email (e.g. the link clicked twice in two tabs,
            // or an admin already approved this one manually in the
            // meantime) falls through to the "sent for review" page
            // below rather than crashing on a unique-email violation.
            if ($signupRequest->plan !== 'company' && ! User::where('email', $signupRequest->email)->exists()) {
                $user = User::create([
                    'name' => $signupRequest->name,
                    'email' => $signupRequest->email,
                    'password' => $signupRequest->password_hash,
                ]);

                // User::$fillable (see App\Models\User) doesn't include
                // email_verified_at - mass-assigning it above would be
                // silently dropped, which left this account stuck
                // "unverified" and looping between onboarding and the
                // verify-email prompt (IdentifyTenant bounces an
                // unverified, business-less user back to onboarding,
                // which then bounces them to verify-email, forever).
                // forceFill bypasses that guard.
                $user->forceFill(['email_verified_at' => now()])->save();

                event(new Registered($user));

                Auth::login($user);
                $request->session()->regenerate();
                $request->session()->put('trial_signup_request_id', $signupRequest->id);

                return redirect()->route('onboarding.create');
            }
        }

        return view('signup-requests.verified');
    }
}
