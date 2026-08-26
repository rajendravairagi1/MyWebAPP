<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level gate for owner-only areas (Team management, Business
 * Settings, Reset Data) — these stay off-limits to a supervisor no
 * matter what modules they're granted, since they control access itself,
 * branding, or can destroy the whole business's data.
 */
class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Tenant::isOwner(), 403);

        return $next($request);
    }
}
