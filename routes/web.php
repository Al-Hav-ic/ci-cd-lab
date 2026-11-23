<?php

declare(strict_types=1);

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Web\CategoryController as WebCategoryController;
use App\Http\Controllers\Web\CustomerController as WebCustomerController;
use App\Http\Controllers\Web\DiscountController as WebDiscountController;
use App\Http\Controllers\Web\OrderController as WebOrderController;
use App\Http\Controllers\Web\ProductController as WebProductController;
use App\Http\Controllers\Web\ReviewController as WebReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'service' => 'supermarket-api',
    'timestamp' => now()->toIso8601String(),
]));

Route::get('/system-info', fn () => response()->json([
    'app' => config('app.name'),
    'laravel' => app()->version(),
    'php' => PHP_VERSION,
    'environment' => app()->environment(),
]));

/*
|--------------------------------------------------------------------------
| Каталог товарів (1:M — Product belongsTo Category)
|--------------------------------------------------------------------------
*/
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::post('/', [ProductController::class, 'store'])
        ->middleware('product.access')
        ->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::put('/{product}', [ProductController::class, 'update'])
        ->middleware('product.access')
        ->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])
        ->middleware('product.access')
        ->name('destroy');
});

// Дубль каталогу з назвою, яку використовує методичка лабораторної №6.
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');
Route::post('/catalog', [ProductController::class, 'store'])
    ->middleware('product.access')
    ->name('catalog.store');
Route::get('/catalog/{product}', [ProductController::class, 'show'])->name('catalog.show');

Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show', 'store']);

Route::apiResource('carts', CartController::class)
    ->only(['index', 'show', 'store']);

Route::post('/carts/{cart}/items', [CartController::class, 'addItem'])->name('carts.items.store');
Route::patch('/carts/{cart}/items/{item}', [CartController::class, 'updateItem'])->name('carts.items.update');
Route::delete('/carts/{cart}/items/{item}', [CartController::class, 'destroyItem'])->name('carts.items.destroy');

Route::apiResource('orders', OrderController::class)
    ->only(['index', 'show', 'store']);
Route::put('/orders/{order}/discounts', [OrderController::class, 'syncDiscounts'])->name('orders.discounts.sync');
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');

Route::apiResource('reviews', ReviewController::class)
    ->only(['index', 'store']);

Route::apiResource('discounts', DiscountController::class)
    ->only(['index', 'store']);

/*
|--------------------------------------------------------------------------
| Вебінтерфейс на Blade (лаб. 7): SSR, макети, компоненти, HTML-форми
|--------------------------------------------------------------------------
| API-маршрути вище залишено недоторканими (лаб. 5–6): вони повертають JSON
| і покриті тестами. Вебсторінки живуть на окремих префiксах:
|   /shop            — CRUD головної сутності Product (1:M -> categories),
|   /manage/...      — решта сутностей предметної галузі,
|   /manage/orders   — 1:M (customer) та M:M (discount_order) через форми.
*/

Route::view('/', 'home')->name('home');

Route::resource('shop', WebProductController::class)
    ->parameters(['shop' => 'product']);

/*
|--------------------------------------------------------------------------
| Керування довідниками (/manage, лаб. 7): лише для автентифікованих
|--------------------------------------------------------------------------
| Деструктивні операції (store/update/destroy) змінюють дані всієї
| предметної області, тому весь розділ закритий посередником auth:
| гостя буде перенаправлено на /login, а необхідність CSRF-токена
| лишається на формі (див. @csrf у Blade-шаблонах).
*/

Route::middleware('auth')->prefix('manage')->name('manage.')->group(function (): void {
    Route::resource('categories', WebCategoryController::class)->except(['show']);
    Route::resource('customers', WebCustomerController::class)->except(['show']);
    Route::resource('discounts', WebDiscountController::class)->except(['show']);
    Route::resource('orders', WebOrderController::class);
    Route::resource('reviews', WebReviewController::class)->except(['show']);
});
