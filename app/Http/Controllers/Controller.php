<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

/**
 * Базовий клас контролерів (лаб. 8).
 *
 * AuthorizesRequests надає метод $this->authorize() — єдиний шар авторизації
 * для веб- (сесії) та API-контролерів (Sanctum) через ProductPolicy.
 *
 * OpenAPI-атрибути нижче формують заголовок документації Swagger UI
 * та схему безпеки bearerAuth (кнопка Authorize).
 */
#[OA\Info(
    version: '1.0.0',
    title: 'API Інформаційної Системи «Супермаркет»',
    description: 'Документація версіонованого REST API (/api/v1) для лабораторної роботи №8: автентифікація Sanctum, авторизація Policies, API Resources.'
)]
#[OA\Server(url: 'http://127.0.0.1:8000/api/v1', description: 'Локальний сервер розробки')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    use AuthorizesRequests;
}
