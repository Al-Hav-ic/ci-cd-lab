<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AbstractEntity;
use App\Models\Customer;
use App\Models\Product;

final class EntityFactory
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createEntity(string $type, array $data): AbstractEntity
    {
        return match ($type) {
            'product' => new Product(
                (string) ($data['id'] ?? uniqid('p_')),
                (string) ($data['createdAt'] ?? date('Y-m-d H:i:s')),
                (string) ($data['name'] ?? 'Без назви'),
                (float) ($data['price'] ?? 0),
                (int) ($data['stockQty'] ?? 0),
                (string) ($data['manufacturer'] ?? 'невідомо'),
            ),
            'customer' => new Customer(
                (string) ($data['id'] ?? uniqid('u_')),
                (string) ($data['createdAt'] ?? date('Y-m-d H:i:s')),
                (string) ($data['fullName'] ?? 'Без імені'),
                (string) ($data['email'] ?? 'no-reply@example.com'),
                (string) ($data['role'] ?? 'customer'),
            ),
            default => throw new \InvalidArgumentException("Невідомий тип сутності: {$type}"),
        };
    }
}
