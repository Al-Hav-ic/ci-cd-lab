<?php

declare(strict_types=1);

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'service' => 'shop-api',
    'timestamp' => now()->toIso8601String(),
]));

Route::get('/system-info', fn () => response()->json([
    'app' => config('app.name'),
    'laravel' => app()->version(),
    'php' => PHP_VERSION,
    'environment' => app()->environment(),
]));

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');

    Route::post('/', [ProductController::class, 'store'])
        ->middleware('product.access')
        ->name('store');

    Route::get('/{id}', [ProductController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});

Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');
