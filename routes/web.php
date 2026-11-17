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
use App\Http\Controllers\WebAuthController;
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

/*
|--------------------------------------------------------------------------
| Автентифікація вебінтерфейсу (лаб. 8): сесії, CSRF, Session Fixation
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Каталог (/shop, лаб. 7): публічний перегляд + захищена зміна даних (лаб. 8)
|--------------------------------------------------------------------------
| Гостей посередник auth перенаправляє на /login; власника запису
| перевіряє ProductPolicy ($this->authorize у контролері).
| Захищені маршрути реєструються ПЕРШИМИ: інакше GET /shop/create
| перехоплюється публічним маршрутом show (GET /shop/{product}).
*/

Route::middleware('auth')->group(function (): void {
    Route::resource('shop', WebProductController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['shop' => 'product']);
});

Route::resource('shop', WebProductController::class)
    ->only(['index', 'show'])
    ->parameters(['shop' => 'product']);

Route::prefix('manage')->name('manage.')->group(function (): void {
    Route::resource('categories', WebCategoryController::class)->except(['show']);
    Route::resource('customers', WebCustomerController::class)->except(['show']);
    Route::resource('discounts', WebDiscountController::class)->except(['show']);
    Route::resource('orders', WebOrderController::class);
    Route::resource('reviews', WebReviewController::class)->except(['show']);
});
