<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * The second step of login for an account with 2FA enabled. By the time
 * a request reaches here, AuthenticatedSessionController has already
 * verified the password and deliberately logged the user back out again
 * — session('2fa.user.id') is the only thing carrying them through this
 * step, so this controller never assumes an authenticated session exists.
 */
class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('2fa.user.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa.user.id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'string']]);

        $this->ensureIsNotRateLimited($userId);

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget(['2fa.user.id', '2fa.remember']);

            return redirect()->route('login');
        }

        $code = str_replace(' ', '', $request->string('code'));

        if ($this->verifyTotpOrRecoveryCode($user, $code)) {
            RateLimiter::clear($this->throttleKey($userId));

            $remember = (bool) $request->session()->get('2fa.remember');
            $request->session()->forget(['2fa.user.id', '2fa.remember']);

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        RateLimiter::hit($this->throttleKey($userId));

        throw ValidationException::withMessages([
            'code' => __('That code is incorrect.'),
        ]);
    }

    protected function verifyTotpOrRecoveryCode(User $user, string $code): bool
    {
        if (preg_match('/^\d{6}$/', $code) && Totp::verify($user->two_factor_secret, $code)) {
            return true;
        }

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        $match = collect($recoveryCodes)->first(fn ($stored) => hash_equals($stored, Str::upper($code)));

        if ($match) {
            $user->two_factor_recovery_codes = array_values(array_diff($recoveryCodes, [$match]));
            $user->save();

            return true;
        }

        return false;
    }

    protected function ensureIsNotRateLimited(int $userId): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($userId), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($userId));

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(int $userId): string
    {
        return 'two-factor|'.$userId.'|'.request()->ip();
    }
}
