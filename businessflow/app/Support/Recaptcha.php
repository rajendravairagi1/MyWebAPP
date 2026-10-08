<?php

namespace App\Support;

/**
 * Google reCAPTCHA v3 verification for the public /get-started form —
 * see config('services.recaptcha'). Only a missing token (the common
 * sign of a bot posting straight to the endpoint without ever loading
 * the page's JS) or a low spam score fails; anything else - Google's
 * API being unreachable, a malformed response - fails OPEN, same
 * philosophy as App\Support\IpGeolocation. A real signup must never be
 * blocked by a flaky third-party API; the honeypot field already
 * catches the simplest bots regardless.
 */
class Recaptcha
{
    public static function passes(?string $token, string $action): bool
    {
        $secret = config('services.recaptcha.secret_key');

        if (! $secret) {
            return true; // Not configured - skip rather than block.
        }

        if (! $token) {
            return false;
        }

        try {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $body = http_build_query([
                'secret' => $secret,
                'response' => $token,
            ]);

            $response = function_exists('curl_init')
                ? static::postViaCurl($url, $body)
                : static::postViaStream($url, $body);

            if (! $response) {
                return true; // Couldn't reach Google at all - don't block on that.
            }

            $data = json_decode($response, true);

            if (! is_array($data) || ! array_key_exists('success', $data)) {
                return true; // Not a well-formed response - can't verify, don't block.
            }

            // A well-formed "success: false" usually means the token was
            // invalid, expired, or reused - a real signal, so this
            // blocks. The one exception: our own secret key being wrong
            // or missing is a setup mistake, not evidence the submitter
            // is a bot - that fails open so a typo in .env can't take
            // down the signup form for every real visitor.
            if (! $data['success']) {
                $configError = array_intersect(
                    $data['error-codes'] ?? [],
                    ['invalid-input-secret', 'missing-input-secret']
                );

                return (bool) $configError;
            }

            if (($data['action'] ?? null) !== $action) {
                return false;
            }

            return (float) ($data['score'] ?? 0) >= (float) config('services.recaptcha.min_score', 0.5);
        } catch (\Throwable) {
            return true;
        }
    }

    // Most shared hosts allow curl even when allow_url_fopen is locked
    // down, so it's tried first; the stream wrapper is the fallback for
    // the rare host without the curl extension at all.
    private static function postViaCurl(string $url, string $body): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ?: null;
    }

    private static function postViaStream(string $url, string $body): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $body,
                'timeout' => 3,
                'ignore_errors' => true,
            ],
        ]);

        return @file_get_contents($url, false, $context) ?: null;
    }
}
