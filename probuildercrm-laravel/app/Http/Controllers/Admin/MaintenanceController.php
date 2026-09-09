<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Same actions as the public /migrate?token=... URL, but reachable from
 * inside the logged-in admin panel — so a deploy isn't blocked on
 * remembering the install token or the exact URL.
 */
class MaintenanceController extends Controller
{
    /**
     * Folders/files a full backup skips - either regenerable (vendor,
     * caches, logs) or irrelevant to restoring the site (.git). Everything
     * else, including anything uploaded through /admin (logo, favicon,
     * blog images) that never lived in git, is included.
     */
    private const BACKUP_EXCLUDE_PATHS = [
        'vendor',
        'node_modules',
        '.git',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/framework/testing',
        'storage/logs',
        'storage/pail',
        'bootstrap/cache',
    ];

    public function index()
    {
        return view('admin.maintenance.index');
    }

    /**
     * The entire database as one file - safe to download directly since
     * this app runs on SQLite (one file = the whole database), rather than
     * needing a mysqldump-style export step.
     */
    public function downloadDatabase(): BinaryFileResponse
    {
        $path = database_path('database.sqlite');

        abort_unless(is_file($path), 404, 'No database file found.');

        return response()->download($path, 'probuildercrm-database-'.now()->format('Y-m-d_His').'.sqlite');
    }

    /**
     * Everything needed to restore the site onto a fresh install of the
     * vendor/ dependencies: application code, config, the live database,
     * and every file ever uploaded through /admin (logo, favicon, blog
     * images) - none of which live in the code zips generated from git.
     */
    public function downloadFullBackup()
    {
        set_time_limit(300);

        // Built outside base_path() entirely - anywhere under it (even
        // storage/app) would have the half-written zip walk into its own
        // directory listing as the RecursiveDirectoryIterator below reaches it.
        $zipPath = sys_get_temp_dir().'/probuildercrm-backup-'.now()->format('Y-m-d_His').'-'.bin2hex(random_bytes(4)).'.zip';
        $base = base_path();

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create the backup archive.');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), strlen($base) + 1);

            if ($this->isExcludedFromBackup($relative)) {
                continue;
            }

            if ($file->isDir()) {
                $zip->addEmptyDir($relative);
            } else {
                $zip->addFile($file->getPathname(), $relative);
            }
        }

        $zip->close();

        return response()->download($zipPath, 'probuildercrm-full-backup-'.now()->format('Y-m-d_His').'.zip')
            ->deleteFileAfterSend(true);
    }

    private function isExcludedFromBackup(string $relativePath): bool
    {
        $relativePath = str_replace('\\', '/', $relativePath);

        foreach (self::BACKUP_EXCLUDE_PATHS as $excluded) {
            if ($relativePath === $excluded || str_starts_with($relativePath, $excluded.'/')) {
                return true;
            }
        }

        return false;
    }

    public function clearCache()
    {
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        $this->clearCompiledViews();

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
        $this->clearCompiledViews();

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

    /**
     * Belt-and-suspenders on top of `view:clear` — a host where that
     * command silently no-ops would otherwise keep serving stale
     * compiled templates indefinitely.
     */
    private function clearCompiledViews(): void
    {
        foreach (glob(storage_path('framework/views/*.php')) ?: [] as $compiled) {
            @unlink($compiled);
        }
    }
}
