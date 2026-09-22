<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets a business restrict itself to "Mobile only" (see
 * Business::ACCESS_MODES) — set from Platform Admin, not by the
 * business owner themselves, since it's tied to what they're paying
 * for. "Mobile" here means any phone/tablet — the Android app, Android
 * Chrome, or an iPhone/iPad's Safari all count, since none of them are
 * the thing this restriction is actually for (a full desktop browser).
 * Detected from the User-Agent string, which is the only signal a
 * server gets for "is this a phone" — like any UA sniffing this is a
 * soft/UX-level restriction (a determined desktop visitor could spoof
 * their UA string), not a hard security boundary. Once verified for
 * this session it's remembered, so a follow-up request that happens to
 * carry an unusual User-Agent never bounces a legitimate mobile user
 * mid-session.
 */
class EnsureAccessChannel
{
    private const MOBILE_UA_PATTERN = '/Mobi|Android|iPhone|iPad|iPod|Windows Phone/i';

    public function handle(Request $request, Closure $next): Response
    {
        if (! Tenant::check()) {
            return $next($request);
        }

        // business.* stays reachable from any device even in "Mobile
        // only" mode — otherwise switching this setting on would
        // immediately lock the owner out of the one page that could
        // switch it back off.
        if ($request->routeIs(['mobile-required', 'logout', 'profile.*', 'business.*'])) {
            return $next($request);
        }

        $business = Business::find(Tenant::id());

        if (! $business || ! $business->isMobileOnly()) {
            return $next($request);
        }

        if ($request->session()->get('verified_mobile_device')) {
            return $next($request);
        }

        if (preg_match(self::MOBILE_UA_PATTERN, (string) $request->userAgent())) {
            $request->session()->put('verified_mobile_device', true);

            return $next($request);
        }

        return redirect()->route('mobile-required');
    }
}
