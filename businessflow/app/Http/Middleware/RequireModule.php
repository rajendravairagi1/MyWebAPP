<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level gate for a module (e.g. Route::middleware('module:investors')).
 * Owners always pass; a supervisor only passes if the module is in their
 * granted permissions. Runs after IdentifyTenant, which is what actually
 * populates Tenant::role()/can().
 */
class RequireModule
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        abort_unless(Tenant::can($module), 403);

        return $next($request);
    }
}
