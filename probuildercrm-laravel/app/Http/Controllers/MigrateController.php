<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * For hosts with no SSH access: after uploading updated code, visiting
 * /migrate?token=<INSTALL_TOKEN> runs any pending migrations, seeds the
 * default pricing plans (only inserts what's missing — never overwrites a
 * price you've already changed from /admin), and clears cached views/config.
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
        Artisan::call('route:clear');
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => 'PricingPlanSeeder', '--force' => true]);

        // Some shared hosts run with opcache.validate_timestamps off, so a
        // freshly uploaded PHP file can keep executing the old cached
        // bytecode until a worker restart — reset it explicitly so a
        // deploy always takes effect immediately.
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return response('<pre>'.e(Artisan::output()).'</pre>');
    }
}
