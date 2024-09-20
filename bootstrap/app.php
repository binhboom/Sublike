<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->use([
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->web([
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Session\Middleware\AuthenticateSession::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AuthSite::class,
        ]);

        $middleware->api([
            Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // alias
        $middleware->alias([
            'xss' => \App\Http\Middleware\XssSite::class,
            'isAdmin' => \App\Http\Middleware\isAdmin::class,

            // install site
            'installSite' => \App\Http\Middleware\InstallSite::class,
            'authSite' => \App\Http\Middleware\AuthSite::class,

            // for api
            'api.auth' => \App\Http\Middleware\ApiAuth::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
