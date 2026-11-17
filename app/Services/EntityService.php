<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Product;
use App\Interfaces\LoggerInterface;

class EntityService
{
    public function __construct(private LoggerInterface $logger) {}

    public function calculateLineTotal(Product $product, int $quantity, float $discountPercent = 0.0): float
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Кількість має бути не меншою за 1.');
        }

        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new \InvalidArgumentException('Неприпустиме значення знижки!');
        }

        $total = $product->getPrice() * $quantity;
        $total -= $total * $discountPercent / 100;

        $this->logger->log(sprintf(
            'Розраховано позицію «%s»: %d шт., знижка %.1f%%, сума %.2f',
            $product->getDetails(),
            $quantity,
            $discountPercent,
            $total,
        ));

        return round($total, 2);
    }

    public function registerProduct(Product $product): void
    {
        $this->logger->log(
            $product->isAvailable()
                ? 'Товар додано в каталог: '.$product->getDetails()
                : 'Товар додано, але немає в наявності: '.$product->getDetails()
        );
    }
}
