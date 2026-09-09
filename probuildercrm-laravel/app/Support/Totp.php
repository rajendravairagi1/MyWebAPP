<?php

namespace App\Support;

/**
 * RFC 6238 TOTP (the algorithm Google Authenticator, Authy, etc. all
 * implement) in plain PHP - no composer package, because this app's deploy
 * process (cPanel zip upload, no SSH/composer) can't pull in a new vendor
 * dependency. HMAC-SHA1, 6 digits, 30-second steps - the universal default
 * every authenticator app assumes when you don't specify otherwise.
 */
class Totp
{
    private const PERIOD = 30;

    private const DIGITS = 6;

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    /**
     * Checks a 6-digit code against the current time step, allowing the
     * previous and next step too (±30s) so a slightly-off device clock or
     * the user typing just as the code rolls over still works.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timestep = intdiv(time(), self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::codeAt($secret, $timestep + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    public static function otpauthUrl(string $secret, string $accountLabel, string $issuer): string
    {
        $label = rawurlencode($issuer).':'.rawurlencode($accountLabel);

        return 'otpauth://totp/'.$label
            .'?secret='.$secret
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits=6&period=30';
    }

    private static function codeAt(string $secret, int $timestep): string
    {
        $key = self::base32Decode($secret);
        $counter = str_repeat("\0", 4).pack('N', $timestep);
        $hash = hash_hmac('sha1', $counter, $key, true);

        $offset = ord($hash[19]) & 0x0f;
        $truncated = ((ord($hash[$offset]) & 0x7f) << 24)
            | ((ord($hash[$offset + 1]) & 0xff) << 16)
            | ((ord($hash[$offset + 2]) & 0xff) << 8)
            | (ord($hash[$offset + 3]) & 0xff);

        return str_pad((string) ($truncated % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $bits = str_pad($bits, (int) (ceil(strlen($bits) / 5) * 5), '0', STR_PAD_RIGHT);

        $encoded = '';
        foreach (str_split($bits, 5) as $chunk) {
            $encoded .= self::ALPHABET[bindec($chunk)];
        }

        return $encoded;
    }

    private static function base32Decode(string $base32): string
    {
        $base32 = strtoupper((string) preg_replace('/[^A-Z2-7]/', '', $base32));

        $bits = '';
        foreach (str_split($base32) as $char) {
            $pos = strpos(self::ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $bytes .= chr(bindec($byte));
            }
        }

        return $bytes;
    }
}
