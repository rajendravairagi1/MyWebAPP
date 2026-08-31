<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment is collected manually outside the app (see Admin\AdminController)
 * — this is what actually enforces it, redirecting to a "renew" page once
 * the active business (or, for a builder under a branch, its Company) is
 * past its subscription_expires_at. Runs right after IdentifyTenant, so
 * Tenant::id() is already resolved for this request. The platform admin
 * is never blocked, since they're the one who'd need to fix it.
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Tenant::check()) {
            return $next($request);
        }

        $email = $request->user()?->email;

        if ($email && $email === config('platform.admin_email')) {
            return $next($request);
        }

        if ($request->routeIs(['subscription.expired', 'logout', 'profile.*'])) {
            return $next($request);
        }

        $business = Business::find(Tenant::id());

        if ($business && $business->isSubscriptionExpired()) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
