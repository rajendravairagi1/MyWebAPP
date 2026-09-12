<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\RunScheduledBackup;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Lets the platform owner see and download the automatic per-business
 * backups saved to storage/app/customer-backups/ (see RunScheduledBackup)
 * without needing file-manager/SSH access to the server — so a customer
 * who needs their data back can be handed it straight from here.
 */
class BackupAdminController extends Controller
{
    public function index(): View
    {
        $disk = Storage::disk('local');
        $names = Business::pluck('name', 'id');

        $backups = collect($disk->files(RunScheduledBackup::DIR))
            ->filter(fn ($path) => str_ends_with($path, '.zip'))
            ->map(function ($path) use ($disk, $names) {
                $filename = basename($path);
                $businessId = $this->businessIdFromFilename($filename);

                return [
                    'filename' => $filename,
                    'business_name' => $businessId !== null ? ($names[$businessId] ?? null) : null,
                    'size' => $disk->size($path),
                    'created_at' => $disk->lastModified($path),
                ];
            })
            ->sortByDesc('created_at')
            ->values();

        return view('admin.backups', compact('backups'));
    }

    public function download(string $filename): StreamedResponse
    {
        $path = $this->safePath($filename);

        return Storage::disk('local')->download($path);
    }

    public function destroy(string $filename): RedirectResponse
    {
        Storage::disk('local')->delete($this->safePath($filename));

        return back()->with('status', 'Backup deleted.');
    }

    public function runNow(): RedirectResponse
    {
        Artisan::call('backup:run');

        return back()->with('status', 'Backup run complete for every business.');
    }

    /**
     * Strips any path segments from the route-supplied filename before
     * touching the disk — this is user input reflected straight from the
     * URL, so without basename() a crafted "../../.env" would try to
     * read outside customer-backups/ entirely.
     */
    private function safePath(string $filename): string
    {
        $filename = basename($filename);
        $path = RunScheduledBackup::DIR.'/'.$filename;

        abort_unless(str_ends_with($filename, '.zip') && Storage::disk('local')->exists($path), 404);

        return $path;
    }

    private function businessIdFromFilename(string $filename): ?int
    {
        // {slug}-{business_id}-{Y-m-d_His}.zip
        if (preg_match('/-(\d+)-\d{4}-\d{2}-\d{2}_\d{6}\.zip$/', $filename, $m)) {
            return (int) $m[1];
        }

        return null;
    }
}
