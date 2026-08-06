<?php

use App\Http\Controllers\MigrateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| One-time migration-runner route
|--------------------------------------------------------------------------
| For hosts without Terminal/SSH access. Runs `migrate` only (never seed,
| never fresh) — safe to keep and reuse for every future update ZIP that
| adds new tables/columns. Delete when no longer needed.
*/

Route::get('/migrate', [MigrateController::class, 'index']);
Route::post('/migrate/run', [MigrateController::class, 'run']);
