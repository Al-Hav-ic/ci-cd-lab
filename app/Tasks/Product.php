<?php

declare(strict_types=1);

namespace App\Tasks;

/**
 * Табл. 4.2 №1 (варіант 6): позиція на складі (id, name, price).
 */
final class Product
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function matchesName(string $query): bool
    {
        return str_contains(mb_strtolower($this->name), mb_strtolower($query));
    }
}
