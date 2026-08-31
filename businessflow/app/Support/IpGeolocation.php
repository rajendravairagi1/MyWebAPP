<?php

namespace App\Support;

/**
 * Country/city lookup for the login audit log — a plain HTTP call to a
 * free geolocation API rather than a bundled GeoIP database, since
 * deploys are zip-upload only (no composer, no room for a multi-MB
 * database file). Fails silently and fast: a login must never be
 * blocked or meaningfully slowed by a flaky/unreachable API.
 */
class IpGeolocation
{
    /**
     * @return array{country: ?string, city: ?string}
     */
    public static function lookup(string $ip): array
    {
        $empty = ['country' => null, 'city' => null];

        if (static::isPrivateOrLocal($ip)) {
            return $empty;
        }

        try {
            $url = 'http://ip-api.com/json/'.urlencode($ip).'?fields=status,country,city';
            $response = function_exists('curl_init') ? static::fetchViaCurl($url) : static::fetchViaStream($url);

            if (! $response) {
                return $empty;
            }

            $data = json_decode($response, true);

            if (! is_array($data) || ($data['status'] ?? null) !== 'success') {
                return $empty;
            }

            return ['country' => $data['country'] ?? null, 'city' => $data['city'] ?? null];
        } catch (\Throwable) {
            return $empty;
        }
    }

    private static function isPrivateOrLocal(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    // Most shared hosts allow curl even when allow_url_fopen is locked
    // down, so it's tried first; the stream wrapper is the fallback for
    // the rare host without the curl extension at all.
    private static function fetchViaCurl(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ?: null;
    }

    private static function fetchViaStream(string $url): ?string
    {
        $context = stream_context_create([
            'http' => ['timeout' => 2, 'ignore_errors' => true],
        ]);

        return @file_get_contents($url, false, $context) ?: null;
    }
}
