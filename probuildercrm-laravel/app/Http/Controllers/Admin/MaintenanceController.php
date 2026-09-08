<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

/**
 * Same actions as the public /migrate?token=... URL, but reachable from
 * inside the logged-in admin panel — so a deploy isn't blocked on
 * remembering the install token or the exact URL.
 */
class MaintenanceController extends Controller
{
    public function index()
    {
        return view('admin.maintenance.index');
    }

    public function clearCache()
    {
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return redirect()->route('admin.maintenance.index')->with('status', 'Caches cleared.');
    }

    public function runMigrations()
    {
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        Artisan::call('db:seed', ['--class' => 'PricingPlanSeeder', '--force' => true]);
        $output .= Artisan::output();

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return redirect()->route('admin.maintenance.index')
            ->with('status', 'Migrations run successfully.')
            ->with('output', $output);
    }
}
