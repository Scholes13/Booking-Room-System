<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Jika perlu, tambahkan middleware global di sini, misal:
        // $middleware->global([
        //     App\Http\Middleware\SomeGlobalMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Konfigurasi exception handling, jika diperlukan.
    })
    ->create();
