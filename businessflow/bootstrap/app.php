<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // IdentifyTenant must run before SubstituteBindings — otherwise a
        // route param like {customer} resolves against the database before
        // the tenant global scope is active, and implicit route-model
        // binding would silently ignore tenant isolation. Laravel's default
        // 'web' group puts SubstituteBindings early, so it's removed and
        // re-appended after our middleware to force the correct order.
        $middleware->removeFromGroup('web', \Illuminate\Routing\Middleware\SubstituteBindings::class);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\IdentifyTenant::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
