<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

class Product extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        string $id,
        string $createdAt,
        private string $name,
        private float $price,
        private int $stockQty,
        private string $manufacturer,
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return sprintf(
            'Товар #%s «%s»: %s грн, на складі %d шт., виробник — %s',
            $this->id,
            $this->name,
            number_format($this->price, 2),
            $this->stockQty,
            $this->manufacturer,
        );
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStockQty(): int
    {
        return $this->stockQty;
    }

    public function isAvailable(): bool
    {
        return $this->stockQty > 0;
    }
}
