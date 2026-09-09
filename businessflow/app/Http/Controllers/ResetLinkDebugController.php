<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

/**
 * One-shot diagnostic that runs the exact same code path as the real
 * "Forgot Password" form (Password::sendResetLink), instead of relying on
 * manually clicking the form and checking inbox/log files by hand. Shows
 * the real broker status and, if it throws, the exact exception - both
 * printed directly on screen. Reuses the install token, same as the other
 * no-SSH diagnostic/maintenance endpoints.
 */
class ResetLinkDebugController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid token.'
        );

        $email = $request->query('email');

        if (! $email) {
            return response('<pre>Add &email=you@example.com to the URL.</pre>');
        }

        $output = "Running Password::sendResetLink(['email' => '{$email}']) - the exact same call the real Forgot Password form makes ...\n\n";

        try {
            $status = Password::sendResetLink(['email' => $email]);
            $output .= "Broker status: {$status}\n";
            $output .= "Translated message: ".__($status)."\n\n";

            $output .= match ($status) {
                Password::RESET_LINK_SENT => "SUCCESS - Laravel reports the notification was dispatched with no exception. If it's still not in the inbox, the failure is on the receiving side (spam filter, mail server delay) rather than in this app.",
                Password::INVALID_USER => "No user account exists with this email address, so nothing was ever sent - this is not a mail delivery problem.",
                Password::RESET_THROTTLED => "Laravel is throttling reset requests for this email (too many recent attempts) - wait 60 seconds and try again, this is not a mail delivery problem.",
                default => "Unrecognised status - see Laravel docs for Password:: constants.",
            };
        } catch (\Throwable $e) {
            $output .= "THREW AN EXCEPTION:\n".get_class($e).': '.$e->getMessage()."\n\n".$e->getTraceAsString();
        }

        return response('<pre style="white-space:pre-wrap;font-family:monospace;padding:20px;">'.e($output).'</pre>');
    }
}
