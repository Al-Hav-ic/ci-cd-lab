<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Версіонований REST API /api/v1 (лаб. 8)
|--------------------------------------------------------------------------
| Публічні: реєстрація/вхід за токеном, читання товарів та довідників.
| Захищені (auth:sanctum): вихід, створення/оновлення/видалення товарів —
| авторизація на рівні моделі через ProductPolicy ($this->authorize).
*/

Route::prefix('v1')->group(function (): void {
    // Публічні маршрути автентифікації та читання.
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::get('/categories', [CatalogApiController::class, 'categories']);
    Route::get('/discounts', [CatalogApiController::class, 'discounts']);
    Route::get('/reviews', [CatalogApiController::class, 'reviews']);

    // Захищені маршрути (вимагають Bearer-токен Sanctum).
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/products', [ProductApiController::class, 'store']);
        Route::put('/products/{product}', [ProductApiController::class, 'update']);
        Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);
    });

    // Поточний користувач за токеном (диагностичний ендпоінт з install:api).
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
