<?php

use App\Http\Middleware\CheckAppClient;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'product.access' => CheckAppClient::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'products',
            'products/*',
            'catalog',
            'catalog/*',
            'categories',
            'categories/*',
            'carts',
            'carts/*',
            'orders',
            'orders/*',
            'reviews',
            'reviews/*',
            'discounts',
            'discounts/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
