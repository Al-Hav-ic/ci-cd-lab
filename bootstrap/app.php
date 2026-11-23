<?php

use App\Http\Middleware\CheckAppClient;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'product.access' => CheckAppClient::class,
        ]);

        // Гости, що потрапили на захищений маршрут, перенаправляються на /login.
        $middleware->redirectGuestsTo(fn (): string => '/login');

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
        // REST API (/api/*) завжди повертає JSON-помилки навіть для клієнтів
        // без заголовка Accept: application/json (інакше 401/403 редіректили
        // б на HTML-сторінку /login) — лабораторна №8.
        $exceptions->shouldRenderJsonWhen(fn ($request, $e) => $request->is('api/*') || $request->expectsJson());
    })->create();
