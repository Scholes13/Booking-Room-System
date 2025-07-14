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
        $middleware->alias([
            'is.admin.bas' => \App\Http\Middleware\AdminBASMiddleware::class,
            'is.sales.mission' => \App\Http\Middleware\SalesMissionMiddleware::class,
            'is.sales.officer' => \App\Http\Middleware\SalesOfficerMiddleware::class,
            'is.lead' => \App\Http\Middleware\IsLead::class,
        ]);

        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Konfigurasi exception handling, jika diperlukan.
    })
    ->create();
