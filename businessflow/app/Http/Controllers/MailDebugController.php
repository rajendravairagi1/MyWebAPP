<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * One-shot diagnostic page for hosts with no SSH access: shows exactly
 * what mail config Laravel is currently running with (removing any doubt
 * about whether an .env edit actually took effect or config is still
 * cached), and optionally attempts a real send so the exact transport
 * exception appears directly on screen instead of requiring a trip
 * through storage/logs/laravel.log. Reuses the install token so only
 * someone who could reach the installer/migrate endpoints can use it.
 */
class MailDebugController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid token.'
        );

        $password = config('mail.mailers.smtp.password');

        $config = [
            'mail.default' => config('mail.default'),
            'mail.mailers.smtp.scheme' => config('mail.mailers.smtp.scheme'),
            'mail.mailers.smtp.host' => config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $password ? '(set, '.strlen((string) $password).' chars)' : '(empty)',
            'mail.from.address' => config('mail.from.address'),
            'mail.from.name' => config('mail.from.name'),
        ];

        $output = "Current live mail config:\n\n";

        foreach ($config as $key => $value) {
            $output .= str_pad($key, 32).': '.(is_null($value) ? '(null)' : $value)."\n";
        }

        $to = $request->query('to');

        if ($to) {
            $output .= "\nAttempting to send a test email to {$to} ...\n\n";

            try {
                Mail::raw('This is a test email from the mail-debug diagnostic page.', function ($message) use ($to) {
                    $message->to($to)->subject('Mail debug test');
                });

                $output .= "SUCCESS - no exception was thrown. Check the inbox (and spam folder) for {$to}.\n";
            } catch (\Throwable $e) {
                $output .= "FAILED with:\n".get_class($e).': '.$e->getMessage()."\n";
            }
        } else {
            $output .= "\nAdd &to=you@example.com to the URL to also attempt sending a real test email and see the exact error here.\n";
        }

        return response('<pre style="white-space:pre-wrap;font-family:monospace;padding:20px;">'.e($output).'</pre>');
    }
}
