<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/admin.php',
            __DIR__ . '/../routes/website.php',
            __DIR__ . '/../routes/application.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tokenfromcookie' => \App\Http\Middleware\TokenFromCookie::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'json' => \App\Http\Middleware\SetJsonHeader::class,
            'verify_tabby_ip' => \App\Http\Middleware\VerifyTabbyIp::class,
            'verify_tamara_token' => \App\Http\Middleware\VerifyTamaraToken::class,
            'verify_myfatoorah_signature' => \App\Http\Middleware\VerifyMyFatoorahSignature::class,

        ]);
        $middleware->prepend(\App\Http\Middleware\SetJsonHeader::class);
        $middleware->priority([
            \App\Http\Middleware\TokenFromCookie::class,
            \App\Http\Middleware\SetJsonHeader::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\VerifyTabbyIp::class,
            \App\Http\Middleware\VerifyTamaraToken::class,
            \App\Http\Middleware\VerifyMyFatoorahSignature::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        });
        Integration::handles($exceptions);
    })->create();
