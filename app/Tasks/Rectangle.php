<?php

declare(strict_types=1);

namespace App\Tasks;

final class Rectangle
{
    public function __construct(
        private readonly float $width,
        private readonly float $height,
    ) {
        if ($width <= 0 || $height <= 0) {
            throw new \InvalidArgumentException('Сторони мають бути додатними.');
        }
    }

    public function perimeter(): float
    {
        return 2 * ($this->width + $this->height);
    }

    public function area(): float
    {
        return $this->width * $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }
}
