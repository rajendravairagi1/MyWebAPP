<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * For hosts with no cron/SSH access to run `php artisan notifications:send-due`
 * on a schedule directly: visiting /run-notifications?token=<INSTALL_TOKEN>
 * — from an external free scheduler (e.g. cron-job.org) every few minutes,
 * or a cPanel Cron Job that just curls a URL instead of running PHP
 * directly. Reuses the installer's token exactly like RunBackupsController.
 */
class RunNotificationsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid token.'
        );

        Artisan::call('notifications:send-due');

        return response('<pre>'.e(Artisan::output()).'</pre>');
    }
}
