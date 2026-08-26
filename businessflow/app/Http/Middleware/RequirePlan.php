<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware alias 'plan:tier' — gates a route behind the active
 * business's subscription tier (solo < team < company). See
 * App\Support\Tenant::planAllows().
 */
class RequirePlan
{
    public function handle(Request $request, Closure $next, string $tier): Response
    {
        abort_unless(Tenant::planAllows($tier), 403);

        return $next($request);
    }
}
