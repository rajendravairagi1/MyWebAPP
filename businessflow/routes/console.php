<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Only takes effect on a host where `php artisan schedule:run` itself is
// actually invoked once a minute by a real cron entry. On a host with no
// cron access, use /run-backups?token=<INSTALL_TOKEN> instead (see
// RunBackupsController) — both ways call the same backup:run command.
Schedule::command('backup:run')->daily()->withoutOverlapping();
