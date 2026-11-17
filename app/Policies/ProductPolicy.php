<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Політика доступу до товарів (лаб. 8).
 *
 * Перегляд — відкритий для всіх (у т.ч. гостей, ?User = guest),
 * створення — будь-який автентифікований користувач,
 * оновлення та видалення — лише власник запису (user_id).
 */
class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }
}
