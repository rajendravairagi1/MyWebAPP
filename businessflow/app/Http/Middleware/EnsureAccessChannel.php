<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets a business restrict itself to "Android app only" (see
 * Business::ACCESS_MODES) — set from Business Settings. Chrome tags
 * every request made inside a verified Trusted Web Activity with an
 * `X-Requested-With: <package name>` header naming the wrapping app,
 * which is the only signal a server actually gets for "this request
 * came from the Android app" — there's no way to verify it more
 * strongly than that, so this is a soft/UX-level restriction (a
 * determined visitor could fake the header), not a hard security
 * boundary. Once verified for this session it's remembered, so a
 * follow-up request that happens not to carry the header (a redirect,
 * an AJAX call) never bounces a legitimate Android user mid-session.
 */
class EnsureAccessChannel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Tenant::check()) {
            return $next($request);
        }

        // business.* stays reachable from a normal browser even in
        // "Android only" mode — otherwise switching this setting on from
        // the web would immediately lock the owner out of the one page
        // that could switch it back off.
        if ($request->routeIs(['android-required', 'logout', 'profile.*', 'business.*'])) {
            return $next($request);
        }

        $business = Business::find(Tenant::id());

        if (! $business || ! $business->isAndroidOnly()) {
            return $next($request);
        }

        if ($request->session()->get('verified_android_app')) {
            return $next($request);
        }

        $expectedPackage = config('app.android_package_name');
        $requestedWith = $request->header('X-Requested-With');

        if ($expectedPackage && $requestedWith === $expectedPackage) {
            $request->session()->put('verified_android_app', true);

            return $next($request);
        }

        return redirect()->route('android-required');
    }
}
