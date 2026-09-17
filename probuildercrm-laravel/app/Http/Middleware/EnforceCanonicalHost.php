<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Google Search Console was flagging http:// and www. copies of every
 * page as "Alternative page with proper canonical tag" - the canonical
 * tag (config('site.url'), non-www https) was already correct, but
 * nothing actually redirected a visitor/crawler landing on one of those
 * other host+scheme combinations to it, so Google kept re-discovering
 * duplicates instead of consolidating them.
 *
 * Scoped to the real production hostnames only (not app()->environment())
 * so this never interferes with local dev/testing on 127.0.0.1 or a
 * staging domain, regardless of what APP_ENV happens to be set to there.
 */
class EnforceCanonicalHost
{
    private const PRODUCTION_HOSTS = ['probuildercrm.com', 'www.probuildercrm.com'];

    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        if (! in_array($host, self::PRODUCTION_HOSTS, true)) {
            return $next($request);
        }

        $canonicalHost = parse_url(config('site.url'), PHP_URL_HOST) ?: 'probuildercrm.com';

        // $request->secure() alone only reflects the direct connection to
        // this server - if a proxy/CDN in front terminates TLS and
        // forwards plain HTTP, that would report false and cause a
        // redirect loop, so X-Forwarded-Proto is also honored here.
        $isHttps = $request->secure() || strtolower((string) $request->header('X-Forwarded-Proto')) === 'https';

        if (! $isHttps || $host !== $canonicalHost) {
            return redirect()->away('https://'.$canonicalHost.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
