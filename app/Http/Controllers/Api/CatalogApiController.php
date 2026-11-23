<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Публічні read-only ендпоінти /api/v1 для решти сутностей (лаб. 8).
 *
 * Демонструє масштабування API-ендпоінтів на всю предметну область
 * з лаб. №2: категорії (1:M), знижки (M:M order_discount), відгуки (1:M).
 * Операції зміни цих сутностей залишаються в API лаб. 5 (/categories, ...).
 */
class CatalogApiController extends Controller
{
    #[OA\Get(
        path: '/categories',
        summary: 'Список категорій товарів (read-only)',
        tags: ['Catalog'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => Category::query()
                ->withCount('products')
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                ]),
        ]);
    }

    #[OA\Get(
        path: '/discounts',
        summary: 'Список знижок, що застосовуються до замовлень (M:M, read-only)',
        tags: ['Catalog'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function discounts(): JsonResponse
    {
        return response()->json([
            'data' => Discount::query()
                ->orderBy('code')
                ->get()
                ->map(fn (Discount $discount) => [
                    'id' => $discount->id,
                    'code' => $discount->code,
                    'percent' => $discount->percent,
                ]),
        ]);
    }

    #[OA\Get(
        path: '/reviews',
        summary: 'Останні схвалені відгуки про товари (read-only)',
        tags: ['Catalog'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function reviews(): JsonResponse
    {
        $reviews = Review::query()
            ->with(['customer.user:id,name'])
            ->where('is_approved', true)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => collect($reviews->items())->map(fn (Review $review) => [
                'id' => $review->id,
                'product_id' => $review->product_id,
                'rating' => $review->rating,
                'text' => $review->text,
                'author' => $review->customer?->user?->name,
            ]),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
        ]);
    }
}
