<?php

use App\Http\Middleware\SetUserIdentifierMiddleware;
use App\Http\Middleware\UpdateLastActivityMiddleware;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi()
            ->alias([
                'update-user-activity' => UpdateLastActivityMiddleware::class,
            ])
            /* ->remove([ */
            /*     HandleCors::class, */
            /* ]) */
            ->append([
                SetUserIdentifierMiddleware::class,
            ]);
            /* ->prepend( */
            /*     CorsMiddleware::class */
            /*         ); */
    })


    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->create();
