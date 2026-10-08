<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Marks this browser/device as belonging to someone who has logged in
 * before, so a later visit to the public /get-started page (also the
 * PWA's start_url - the page a logged-out Android app opens to fresh)
 * defaults to the Login tab instead of Sign Up - see
 * Public\SignupRequestController::show(). Without this, someone who
 * signed up once, then later opened the app logged-out (session
 * expired, reinstalled, new device), landed back on the signup form
 * every single time instead of login.
 *
 * Purely a UX hint, not authentication - a missing or cleared cookie
 * just means Sign Up shows by default again, same as a first-time
 * visitor. Never gates access on its own.
 */
class ReturningUser
{
    private const COOKIE = 'has_logged_in';

    public static function remember(): void
    {
        Cookie::queue(Cookie::forever(self::COOKIE, '1'));
    }

    public static function isKnown(Request $request): bool
    {
        return $request->hasCookie(self::COOKIE);
    }
}
