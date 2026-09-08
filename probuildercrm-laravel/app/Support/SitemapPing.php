<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * "Submitting" a sitemap to a search engine, classically done by pinging
 * https://www.google.com/ping?sitemap=... — Google retired that specific
 * endpoint in 2023, but Bing's still live, and both engines already
 * discover the sitemap on their own via the `Sitemap:` line in
 * robots.txt. This just hints at a faster recrawl; it never blocks
 * whatever triggered it (a blog save) — failures are logged and ignored.
 */
class SitemapPing
{
    public static function ping(): void
    {
        $sitemapUrl = route('sitemap');

        foreach ([
            'bing' => 'https://www.bing.com/ping?sitemap='.urlencode($sitemapUrl),
            'google' => 'https://www.google.com/ping?sitemap='.urlencode($sitemapUrl),
        ] as $engine => $pingUrl) {
            try {
                Http::timeout(3)->get($pingUrl);
            } catch (\Throwable $e) {
                Log::info("Sitemap ping to {$engine} failed: ".$e->getMessage());
            }
        }

        SiteSetting::set('sitemap_last_pinged_at', now()->toDateTimeString());
    }
}
