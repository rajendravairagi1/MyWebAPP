<?php

namespace App\Support;

/**
 * Hand-rolled — no composer package, since deploys are zip-upload only
 * (same constraint that ruled out PhpSpreadsheet for Reports). Covers
 * the common browsers/platforms well enough for a login audit log;
 * doesn't aim for exhaustive User-Agent coverage.
 */
class UserAgentParser
{
    /**
     * @return array{device_type: string, platform: string, browser: string}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = $userAgent ?? '';

        return [
            'device_type' => static::deviceType($ua),
            'platform' => static::platform($ua),
            'browser' => static::browser($ua),
        ];
    }

    private static function deviceType(string $ua): string
    {
        if (preg_match('/iPad|Nexus 7|Nexus 10|KFAPWI|Tablet(?!.*Mobile)/i', $ua)) {
            return 'Tablet';
        }

        if (preg_match('/Mobile|iPhone|iPod|Android.*Mobile|BlackBerry|Windows Phone|Opera Mini/i', $ua)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private static function platform(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/Windows Phone/i', $ua) => 'Windows Phone',
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/Macintosh|Mac OS X/i', $ua) => 'macOS',
            (bool) preg_match('/CrOS/i', $ua) => 'Chrome OS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => 'Other',
        };
    }

    private static function browser(string $ua): string
    {
        // Order matters — Edge/Opera/Samsung UAs also contain "Chrome" and
        // "Safari", so the more specific tokens must be checked first.
        return match (true) {
            (bool) preg_match('/Edg\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/SamsungBrowser/i', $ua) => 'Samsung Internet',
            (bool) preg_match('/FBAN|FBAV/i', $ua) => 'Facebook In-App',
            (bool) preg_match('/Instagram/i', $ua) => 'Instagram In-App',
            (bool) preg_match('/CriOS/i', $ua) => 'Chrome',
            (bool) preg_match('/Firefox\/|FxiOS/i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome\//i', $ua) => 'Chrome',
            (bool) preg_match('/Safari\//i', $ua) => 'Safari',
            default => 'Other',
        };
    }
}
