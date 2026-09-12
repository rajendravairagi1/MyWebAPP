<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * For hosts with no cron/SSH access to run `php artisan backup:run` on a
 * schedule directly: visiting /run-backups?token=<INSTALL_TOKEN> — from
 * an external free scheduler (e.g. cron-job.org) or a cPanel Cron Job
 * that just curls a URL instead of running PHP directly — backs up every
 * business and prunes old copies, exactly like the artisan command does.
 * Reuses the installer's token exactly like MigrateController.
 */
class RunBackupsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid token.'
        );

        Artisan::call('backup:run');

        return response('<pre>'.e(Artisan::output()).'</pre>');
    }
}
