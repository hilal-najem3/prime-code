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

        $middleware->api(prepend: [
            TenantResolver::class,
        ]);

        $middleware->web(append: [
            TenantResolver::class,
            SetLocale::class,
        ]);

        $middleware->alias([
            'access' => AccessMiddleware::class,
            'module' => \App\Http\Middleware\ModuleMiddleware::class,
            'platform.tenant' => \App\Http\Middleware\PlatformTenantResolver::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
