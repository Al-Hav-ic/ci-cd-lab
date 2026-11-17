<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Товар супермаркету (Eloquent-модель, лабораторна №6).
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'sku',
        'name',
        'description',
        'price',
        'stock_qty',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'stock_qty' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Зв'язок Many-to-One — товар належить конкретній категорії.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Зв'язок Many-to-One — товар має власника-користувача (лабораторна №8).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock_qty > 0;
    }
}
