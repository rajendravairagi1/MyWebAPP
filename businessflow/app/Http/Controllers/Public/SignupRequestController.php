<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\SignupRequestVerificationMail;
use App\Models\SignupRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The public, no-login "I'd like an account" form a QR code (or a plain
 * shared link) on Platform Admin points a prospect at — the account-
 * request counterpart to the per-business Lead form. A submission never
 * creates a login by itself: it lands as a pending SignupRequest that
 * only Platform Admin can approve (see Admin\SignupRequestAdminController),
 * same review step the manual "Add Customer Account" flow always had,
 * just fed by the customer's own typing instead of Rajendra retyping it.
 */
class SignupRequestController extends Controller
{
    public function show(): View
    {
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
     * what actually moves the request into Admin's 'pending' queue —
     * see SignupRequest::isVerified() and Admin\SignupRequestAdminController.
     */
    public function verify(Request $request, SignupRequest $signupRequest): View
    {
        abort_unless(hash_equals(sha1($signupRequest->email), (string) $request->route('hash')), 403);

        if (! $signupRequest->isVerified() && $signupRequest->status === 'unverified') {
            $signupRequest->update(['status' => 'pending', 'email_verified_at' => now()]);
        }

        return view('signup-requests.verified');
    }
}
