<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * For hosts with no SSH access: after uploading updated code, visiting
 * /migrate?token=<INSTALL_TOKEN> runs any pending migrations and clears
 * cached views/config. Reuses the same token as the installer (see
 * InstallController) — it only makes sense once the app is already
 * installed, and only someone who could reach the installer should be
 * able to trigger this either.
 */
class MigrateController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expected = config('app.install_token');

        abort_unless(
            filled($expected) && hash_equals($expected, (string) $request->query('token')),
            403,
            'Missing or invalid token.'
        );

        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('migrate', ['--force' => true]);

        return response('<pre>'.e(Artisan::output()).'</pre>');
    }
}
