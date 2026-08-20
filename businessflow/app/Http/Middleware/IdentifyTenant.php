<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active business for this request from the authenticated
 * user's session, verifies membership, and binds it to App\Support\Tenant.
 * The business_id NEVER comes from the request itself (route param, query
 * string, body) — only from session + a membership check against
 * business_user. Users with no business yet are sent to onboarding.
 */
class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $businessId = $request->session()->get('active_business_id');

        if (! $businessId || ! $user->businesses()->where('businesses.id', $businessId)->exists()) {
            $businessId = $user->businesses()->oldest('business_user.created_at')->value('businesses.id');

            if ($businessId) {
                $request->session()->put('active_business_id', $businessId);
            }
        }

        if (! $businessId) {
            if ($request->routeIs('onboarding.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('onboarding.create');
        }

        Tenant::set($businessId);

        return $next($request);
    }
}
