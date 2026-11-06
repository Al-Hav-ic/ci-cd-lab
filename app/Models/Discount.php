<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Промокод/знижка. Зв'язок M:M з Order через discount_order.
 */
class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'percent',
        'valid_from',
        'valid_to',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'percent' => 'float',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->valid_from->isPast()
            && $this->valid_to->isFuture();
    }
}
