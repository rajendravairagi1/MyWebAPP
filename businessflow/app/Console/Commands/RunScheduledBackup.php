<?php

namespace App\Console\Commands;

use App\Http\Controllers\BackupController;
use App\Models\Business;
use App\Support\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A full data+media backup for every business on this install, saved to
 * storage/app/backups/. Meant to be wired up as a cPanel cron job (see
 * `php artisan schedule:run` below) so a breach, a bad restore, or a
 * server-level crash never means data is gone for good — the same kind
 * of protection "Data-wipe on hack" asked for, without any automatic
 * destructive trigger that a false alarm could set off by mistake.
 *
 * cPanel cron entry (once every 24 hours, adjust the path to your
 * install): * you must adjust /home/USER/businessflow to your actual
 * cPanel path.
 *   0 2 * * * php /home/USER/businessflow/artisan backup:run >> /dev/null 2>&1
 */
class RunScheduledBackup extends Command
{
    protected $signature = 'backup:run {--keep=7 : How many backups to keep per business}';

    protected $description = 'Back up every business\'s data + media to storage/app/backups, pruning old ones.';

    public function handle(BackupController $backupController): int
    {
        $keep = (int) $this->option('keep');
        $disk = Storage::disk('local');

        foreach (Business::all() as $business) {
            $zipPath = Tenant::runAs($business->id, fn () => $backupController->buildZip($business));

            $filename = 'backups/'.Str::slug($business->name).'-'.$business->id.'-'.now()->format('Y-m-d_His').'.zip';
            $disk->put($filename, file_get_contents($zipPath));
            @unlink($zipPath);

            $this->info("Backed up \"{$business->name}\" -> storage/app/{$filename}");

            $this->prune($disk, Str::slug($business->name).'-'.$business->id.'-', $keep);
        }

        return self::SUCCESS;
    }

    private function prune($disk, string $prefix, int $keep): void
    {
        $files = collect($disk->files('backups'))
            ->filter(fn ($path) => str_starts_with(basename($path), $prefix))
            ->sortByDesc(fn ($path) => $disk->lastModified($path))
            ->values();

        $files->slice($keep)->each(fn ($path) => $disk->delete($path));
    }
}
