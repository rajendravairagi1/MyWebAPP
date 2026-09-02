<?php

namespace App\Http\Controllers;

use App\Support\DocumentQr;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Google Authenticator (TOTP) two-factor authentication, managed from
 * Profile Settings. Enabling is a two-step confirm (a secret isn't
 * persisted until the user proves they actually scanned it and can
 * produce a valid code), matching how every other 2FA setup flow works.
 */
class TwoFactorController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $qrCodeUri = null;
        $secret = null;

        if (! $user->hasEnabledTwoFactor()) {
            $secret = $request->session()->get('two_factor.pending_secret');

            if (! $secret) {
                $secret = Totp::generateSecret();
                $request->session()->put('two_factor.pending_secret', $secret);
            }

            $otpAuthUrl = Totp::getOtpAuthUrl(
                config('app.name', 'BusinessFlow'),
                $user->email,
                $secret,
            );

            $qrCodeUri = DocumentQr::dataUri($otpAuthUrl, 200);
        }

        return view('profile.two-factor', [
            'user' => $user,
            'qrCodeUri' => $qrCodeUri,
            'secret' => $secret,
            'recoveryCodes' => $request->session()->get('two_factor.recovery_codes'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $user = $request->user();
        $secret = $request->session()->get('two_factor.pending_secret');

        abort_if(! $secret, 422, 'Your setup session expired — reload this page and scan the QR code again.');

        if (! Totp::verify($secret, $request->string('code'))) {
            return back()->withErrors(['code' => __('That code is incorrect — check the app and try again.')]);
        }

        $codes = collect(range(1, 8))->map(fn () => Str::upper(Str::random(4).'-'.Str::random(4)))->all();

        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $codes;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('two_factor.pending_secret');
        $request->session()->flash('two_factor.recovery_codes', $codes);

        return redirect()->route('two-factor.show')->with('status', __('Two-factor authentication is now enabled. Save these recovery codes somewhere safe.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('two-factor.show')->with('status', __('Two-factor authentication has been disabled.'));
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->hasEnabledTwoFactor(), 422);

        $codes = collect(range(1, 8))->map(fn () => Str::upper(Str::random(4).'-'.Str::random(4)))->all();
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        $request->session()->flash('two_factor.recovery_codes', $codes);

        return redirect()->route('two-factor.show')->with('status', __('New recovery codes generated — your old codes no longer work.'));
    }
}
