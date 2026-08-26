<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$builder = Application::configure(basePath: dirname(__DIR__))
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

        $middleware->alias([
            'module' => \App\Http\Middleware\RequireModule::class,
            'owner' => \App\Http\Middleware\EnsureOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    });

$app = $builder->create();

// Some shared hosts won't let you point the domain's document root at
// public/, so the contents of public/ get moved up to sit alongside
// this app's other folders (vendor, app, bootstrap) instead. Checking
// for the Vite manifest directly (rather than just whether public/
// still exists as a directory) means this also works if an emptied-out
// public/ folder was left behind rather than deleted. When the built
// assets are found one level up instead of the usual place, point
// Laravel's public path at the base path so asset helpers (the Vite
// manifest, asset()) resolve to where the files actually ended up.
if (! is_file($app->basePath('public/build/manifest.json')) && is_file($app->basePath('build/manifest.json'))) {
    $app->usePublicPath($app->basePath());
}

return $app;
