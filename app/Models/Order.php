<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Замовлення покупця.
 *
 * Зв'язки: N:1 Customer, 1:M OrderItem, 1:1 Payment, M:M Discount.
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'customer_id',
        'status',
        'total_amount',
        'discount_amount',
        'delivery_address',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Зв'язок 1:1 — одне замовлення має один платіж.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Зв'язок M:M — замовлення може використовувати кілька знижок.
     */
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class)->withTimestamps();
    }
}
