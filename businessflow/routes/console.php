<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs only if something actually calls `schedule:run` — on shared
// cPanel hosting that means adding ONE cron job that calls it every
// minute (see the class doc-comment on RunScheduledBackup for the
// backup-specific details); Laravel decides from this schedule whether
// each invocation is actually due to run.
Schedule::command('backup:run')->daily()->at('02:00');
