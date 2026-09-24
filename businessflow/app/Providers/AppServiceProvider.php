<?php

namespace App\Providers;

use App\View\Composers\NotificationComposer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', NotificationComposer::class);

        // Sends the "verify your email" link a fresh self-signup needs —
        // see App\Models\User (implements MustVerifyEmail) and the
        // corresponding email_verified_at backfill migration, which is
        // what keeps this from also demanding verification retroactively
        // from every account that already existed before this feature.
        Event::listen(Registered::class, SendEmailVerificationNotification::class);
    }
}
