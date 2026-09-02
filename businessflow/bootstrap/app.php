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
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\IdentifyTenant::class,
            \App\Http\Middleware\EnsureSubscriptionActive::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\NoCacheForAuthenticatedPages::class,
        ]);

        $middleware->alias([
            'module' => \App\Http\Middleware\RequireModule::class,
            'owner' => \App\Http\Middleware\EnsureOwner::class,
            'plan' => \App\Http\Middleware\RequirePlan::class,
            'platform-admin' => \App\Http\Middleware\EnsurePlatformAdmin::class,
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
// this app's other folders (vendor, app, bootstrap) instead. A plain
// Vite project never outputs a build/ directory at the project root —
// only inside public/ — so finding one at the base path is an
// unambiguous signal of this flattened layout. It's checked first and
// wins even if a stale public/build/ happens to still exist too (e.g.
// left over from before the deploy switched to this flattened form):
// otherwise every future deploy's fresh assets would keep silently
// losing to that old, orphaned copy no one remembers to update.
if (is_file($app->basePath('build/manifest.json'))) {
    $app->usePublicPath($app->basePath());
}

return $app;
