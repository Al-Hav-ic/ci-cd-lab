<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource головної сутності Product (лаб. 8).
 *
 * Визначає чіткий контракт JSON-відповіді: замість «сирої» моделі Eloquent
 * клієнт отримує лише публічні поля; зв'язки включаються умовно
 * через whenLoaded(), що уникає проблеми N + 1 запитів.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'stock_qty' => $this->stock_qty,
            'is_active' => (bool) $this->is_active,
            'owner_id' => $this->user_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'reviews' => $this->whenLoaded('reviews', fn () => $this->reviews->map(fn ($review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'text' => $review->text,
            ])),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
