<?php

use App\Http\Middleware\AccessMiddleware;
use App\Http\Middleware\TenantResolver;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'tenant' => TenantResolver::class,
            'access' => AccessMiddleware::class,
            'module' => \App\Http\Middleware\ModuleMiddleware::class,
            'platform.tenant' => \App\Http\Middleware\PlatformTenantResolver::class,
        ]);

        // ✅ THIS is what you're missing
        $middleware->priority([
            \App\Http\Middleware\TenantResolver::class,
            \App\Http\Middleware\PlatformTenantResolver::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
