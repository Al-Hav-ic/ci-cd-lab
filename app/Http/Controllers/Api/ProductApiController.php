<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * CRUD головної сутності Product під /api/v1 (лаб. 8).
 *
 * Автентифікація — Bearer-токен Sanctum (auth:sanctum), авторизація —
 * ProductPolicy через $this->authorize(): update/delete лише для власника,
 * інакше HTTP 403 Forbidden. Відповіді — JSON через ProductResource.
 */
class ProductApiController extends Controller
{
    #[OA\Get(
        path: '/products',
        summary: 'Отримати список товарів із пагінацією',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        // Жадібне завантаження зв'язків, щоб whenLoaded() їх серіалізував.
        $products = Product::query()
            ->with(['category:id,name', 'reviews:id,product_id,rating,text'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return ProductResource::collection($products);
    }

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Отримати один товар за ідентифікатором',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання запису'),
            new OA\Response(response: 404, description: 'Запис не знайдено'),
        ]
    )]
    public function show(Product $product): ProductResource
    {
        $product->load(['category:id,name', 'reviews:id,product_id,rating,text']);

        return new ProductResource($product);
    }

    #[OA\Post(
        path: '/products',
        summary: 'Створити новий товар (потрібен Bearer-токен)',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'sku', 'price', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ноутбук Pro 16'),
                    new OA\Property(property: 'sku', type: 'string', example: 'NB-2026-01'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 45999.99),
                    new OA\Property(property: 'stock_qty', type: 'integer', example: 10),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Потужний ноутбук для роботи'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Товар успішно створено'),
            new OA\Response(response: 401, description: 'Неавторизований доступ (відсутній токен)'),
            new OA\Response(response: 422, description: 'Помилка валідації вхідних даних'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Політика: створення дозволено будь-якому автентифікованому користувачу.
        $this->authorize('create', Product::class);

        $product = Product::create([
            ...$validated,
            // Запис прив'язується до власника за токеном (не довіряємо запиту).
            'user_id' => $request->user()->id,
        ]);

        return (new ProductResource($product->load('category:id,name')))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Оновити товар (лише власник запису)',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'sku', 'price', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ноутбук Pro 16 (оновлено)'),
                    new OA\Property(property: 'sku', type: 'string', example: 'NB-2026-01'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 43999.00),
                    new OA\Property(property: 'stock_qty', type: 'integer', example: 8),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Товар успішно оновлено'),
            new OA\Response(response: 401, description: 'Неавторизований доступ (відсутній токен)'),
            new OA\Response(response: 403, description: 'Forbidden — користувач не є власником запису (ProductPolicy)'),
            new OA\Response(response: 422, description: 'Помилка валідації вхідних даних'),
        ]
    )]
    public function update(Request $request, Product $product): ProductResource
    {
        // Політика: оновлювати дозволено лише власнику запису, інакше 403.
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update($validated);

        return new ProductResource($product->load('category:id,name'));
    }

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Видалити товар (лише власник запису)',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Товар успішно видалено'),
            new OA\Response(response: 401, description: 'Неавторизований доступ (відсутній токен)'),
            new OA\Response(response: 403, description: 'Forbidden — користувач не є власником запису (ProductPolicy)'),
            new OA\Response(response: 404, description: 'Запис не знайдено'),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        // Політика: видаляти дозволено лише власнику запису, інакше 403.
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Товар успішно видалено.']);
    }
}
