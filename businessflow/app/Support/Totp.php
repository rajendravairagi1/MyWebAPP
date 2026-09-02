<?php

namespace App\Support;

/**
 * A minimal, dependency-free TOTP (RFC 6238) implementation — Google
 * Authenticator, Authy, and every other standard authenticator app
 * implement this same algorithm, so no vendor package is needed to
 * interoperate with them. Deliberately self-contained (own Base32
 * encode/decode, plain hash_hmac) rather than pulling in a Composer
 * package: this app deploys as a zip extracted by hand in cPanel File
 * Manager with no `composer install` step, so every new vendor
 * dependency is a real risk of a broken/partial extraction taking the
 * whole site down — a risk not worth taking for an algorithm this small.
 */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const PERIOD = 30;

    private const DIGITS = 6;

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function getOtpAuthUrl(string $issuer, string $label, string $secret): string
    {
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);

        return 'otpauth://totp/'.rawurlencode($issuer).':'.rawurlencode($label).'?'.$params;
    }

    /**
     * Accepts a code from the current or the immediately adjacent 30s
     * windows either side — small clock drift between the phone and the
     * server is common and shouldn't lock someone out.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timeSlice = (int) floor(time() / self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::codeAt($secret, $timeSlice + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    public static function currentCode(string $secret): string
    {
        return self::codeAt($secret, (int) floor(time() / self::PERIOD));
    }

    private static function codeAt(string $secret, int $timeSlice): string
    {
        $key = self::base32Decode($secret);
        $time = pack('N*', 0).pack('N*', $timeSlice);

        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0xf;

        $truncated = ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff);

        return str_pad((string) ($truncated % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $binary): string
    {
        $bits = '';
        foreach (str_split($binary) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= self::ALPHABET[bindec($chunk)];
        }

        return $output;
    }

    private static function base32Decode(string $encoded): string
    {
        $encoded = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $encoded));

        $bits = '';
        foreach (str_split($encoded) as $char) {
            $pos = strpos(self::ALPHABET, $char);

            if ($pos === false) {
                continue;
            }

            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }

        return $binary;
    }
}
