<?php

use App\Http\Controllers\GooglePlayNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Google Play's Real-time Developer Notifications land here — see
// GooglePlayNotificationController's own doc comment, and
// PLAY_BILLING_SETUP.md step 7 for the Pub/Sub configuration that points
// at this URL. Deliberately outside the 'web' middleware group (no CSRF
// token exists for Google to send, and no tenant/session context makes
// sense for a server-to-server push).
Route::post('/webhooks/google-play-rtdn', GooglePlayNotificationController::class);
