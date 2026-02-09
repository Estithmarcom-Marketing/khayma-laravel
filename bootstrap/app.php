<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/website.php',
            __DIR__.'/../routes/application.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tokenfromcookie' => \App\Http\Middleware\TokenFromCookie::class,
            'locale' => \App\Http\Middleware\SetLocale::class,

        ]);
        $middleware->redirectGuestsTo(fn (Request $request) => null);
        $middleware->priority([
            \App\Http\Middleware\TokenFromCookie::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\SetLocale::class, ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
