<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Every authenticated page here is per-user, per-request dynamic data —
 * dashboards, ledgers, customer records — never safe for a shared cache
 * (browser, CDN, or a host's own page-cache module such as LiteSpeed's,
 * which several cPanel hosts enable server-wide with no per-account
 * toggle) to serve a stale snapshot of after a deploy. Explicit no-store
 * headers are the one thing that reliably stops an intermediate cache
 * from doing that, regardless of how the host has its own caching
 * configured — this is what stopped a freshly deployed page from
 * showing up until the host's cache entry happened to expire.
 */
class NoCacheForAuthenticatedPages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
