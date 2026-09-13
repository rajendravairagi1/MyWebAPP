<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->hasEnabledTwoFactor()) {
            // The password check above already logged this user in via
            // Auth::attempt() — undo that immediately and hold them at
            // the 2FA challenge instead, so a real session only exists
            // once the second factor is verified too.
            Auth::logout();

            $request->session()->put('2fa.user.id', $user->id);
            $request->session()->put('2fa.remember', $request->boolean('remember'));

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(config('app.marketing_url'));
    }

    /**
     * Same effect as destroy() above - the idle-timeout JS in
     * layouts.app (see resources/views/layouts/app.blade.php) submits a
     * hidden form here after 5 minutes with no mouse/keyboard activity.
     * Redirects back to this app's own login page with an explanation
     * instead of the marketing site: landing on a different domain with
     * no context right after an automatic logout would be confusing.
     */
    public function idleLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', __('You were logged out after 5 minutes of inactivity - please log in again.'));
    }
}
