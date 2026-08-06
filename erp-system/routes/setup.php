<?php

use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| One-time installer routes
|--------------------------------------------------------------------------
| For hosts without Terminal/SSH access. Delete this file and
| app/Http/Controllers/SetupController.php once setup is confirmed working
| — see DEPLOY-CPANEL.md step 5.
*/

Route::get('/setup', [SetupController::class, 'index']);
Route::post('/setup/run', [SetupController::class, 'run']);
