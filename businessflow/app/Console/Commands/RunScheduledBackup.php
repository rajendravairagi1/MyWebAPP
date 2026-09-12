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
 * its own dedicated storage/app/customer-backups/ folder (kept separate
 * from everything else in storage/app), one zip per business named after
 * it so it can be handed back to that customer if they ever need it.
 * Anything older than --keep-days is deleted automatically so this
 * folder never grows without bound.
 *
 * Wire this up one of two ways, whichever this host supports:
 *
 * 1) A cPanel Cron Job running once a day:
 *      0 2 * * * php /home/USER/businessflow/artisan backup:run >> /dev/null 2>&1
 *    (adjust /home/USER/businessflow to the real install path)
 *
 * 2) No cron access at all: an external free scheduler (e.g.
 *    cron-job.org) hitting /run-backups?token=<INSTALL_TOKEN> once a day
 *    — see RunBackupsController, which just calls this same command.
 */
class RunScheduledBackup extends Command
{
    public const DIR = 'customer-backups';

    protected $signature = 'backup:run {--keep-days=10 : Delete backups older than this many days}';

    protected $description = 'Back up every business\'s data + media to storage/app/customer-backups, deleting old ones.';

    public function handle(BackupController $backupController): int
    {
        $keepDays = (int) $this->option('keep-days');
        $disk = Storage::disk('local');

        foreach (Business::all() as $business) {
            $zipPath = Tenant::runAs($business->id, fn () => $backupController->buildZip($business));

            $filename = self::DIR.'/'.Str::slug($business->name).'-'.$business->id.'-'.now()->format('Y-m-d_His').'.zip';
            $disk->put($filename, file_get_contents($zipPath));
            @unlink($zipPath);

            $this->info("Backed up \"{$business->name}\" -> storage/app/{$filename}");
        }

        $this->pruneOlderThan($disk, $keepDays);

        return self::SUCCESS;
    }

    private function pruneOlderThan($disk, int $days): void
    {
        $cutoff = now()->subDays($days)->timestamp;

        $old = collect($disk->files(self::DIR))
            ->filter(fn ($path) => str_ends_with($path, '.zip') && $disk->lastModified($path) < $cutoff);

        $old->each(function ($path) use ($disk) {
            $disk->delete($path);
            $this->info('Deleted old backup -> storage/app/'.$path);
        });
    }
}
