<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Country/currency detection for pricing. Never blocks or breaks a page
 * load — every failure mode (no database file yet, corrupt file, IP not
 * found, geoip2/geoip2 package missing) falls through to showing the
 * default currency (INR) rather than throwing.
 */
class Geo
{
    public const COOKIE = 'preferred_currency';

    public static function databasePath(): string
    {
        return storage_path('app/geoip/GeoLite2-Country.mmdb');
    }

    public static function databaseInstalled(): bool
    {
        return file_exists(self::databasePath());
    }

    /**
     * The currency to show this request: an explicit manual choice (the
     * pricing page's dropdown, stored in a cookie) always wins over
     * geo-detection, since IP-based country lookup is a best guess and a
     * visitor who corrects it should stay corrected for their visit.
     */
    public static function currencyForRequest(Request $request): string
    {
        $cookieChoice = $request->cookie(self::COOKIE);

        if ($cookieChoice && Currency::isSupported($cookieChoice)) {
            return $cookieChoice;
        }

        return Currency::forCountry(self::countryForIp($request->ip()));
    }

    public static function countryForIp(?string $ip): ?string
    {
        if (! $ip || ! self::databaseInstalled() || ! class_exists(\GeoIp2\Database\Reader::class)) {
            return null;
        }

        try {
            $reader = new \GeoIp2\Database\Reader(self::databasePath());

            return $reader->country($ip)->country->isoCode;
        } catch (\Throwable $e) {
            // A local/private IP (common when testing) or one missing from
            // the database both land here — neither is worth logging noise
            // for on every single page view.
            return null;
        }
    }
}
