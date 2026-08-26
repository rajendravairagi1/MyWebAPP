<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active business for this request from the authenticated
 * user's session, verifies membership, and binds it (plus this user's
 * role/permissions within it) to App\Support\Tenant. The business_id
 * NEVER comes from the request itself (route param, query string, body)
 * — only from session + a membership check against business_user. Users
 * with no business yet are sent to onboarding.
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
        $membership = $businessId
            ? $user->businesses()->where('businesses.id', $businessId)->first()
            : null;

        if (! $membership) {
            $membership = $user->businesses()->oldest('business_user.created_at')->first();

            if ($membership) {
                $businessId = $membership->id;
                $request->session()->put('active_business_id', $businessId);
            } else {
                $businessId = null;
            }
        }

        if (! $businessId) {
            // A Company Owner/Branch Manager may legitimately have zero
            // business memberships yet (e.g. a branch with no builders
            // added so far) — let them through to set that up instead of
            // forcing single-business onboarding.
            $exemptRoutes = ['onboarding.*', 'logout', 'company.*', 'branches.*', 'builders.*', 'businesses.switch', 'admin.*'];

            if ($request->routeIs($exemptRoutes)) {
                return $next($request);
            }

            // Post-login lands everyone on /dashboard by default — a
            // Company Owner or Branch Manager with no business of their
            // own yet needs to land on their Company/Branch dashboard
            // instead of being bounced into solo-business onboarding.
            if ($user->ownedCompany) {
                return redirect()->route('company.show');
            }

            if ($branch = $user->managedBranches()->first()) {
                return redirect()->route('branches.show', $branch);
            }

            return redirect()->route('onboarding.create');
        }

        Tenant::set($businessId, $membership->pivot->role, $membership->pivot->permissions, $membership->effectivePlan());

        return $next($request);
    }
}
