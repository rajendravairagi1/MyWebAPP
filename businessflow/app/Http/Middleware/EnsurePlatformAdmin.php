<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware alias 'platform-admin' — gates the /admin panel
 * (create customer accounts, set their plan) to a single email set in
 * config('platform.admin_email'), i.e. you. Deliberately not a DB
 * column/role: this is the one person above every Company/Business in
 * the whole app, so there's nothing to look up per-tenant.
 */
class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->user()?->email;

        abort_unless($email && $email === config('platform.admin_email'), 403);

        return $next($request);
    }
}
