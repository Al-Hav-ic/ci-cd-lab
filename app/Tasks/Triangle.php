<?php

declare(strict_types=1);

namespace App\Tasks;

/**
 * Табл. 4.4 №2 (варіант 6): периметр і площа (формула Герона).
 */
final class Triangle
{
    public function __construct(
        private float $sideA,
        private float $sideB,
        private float $sideC,
    ) {
        if ($sideA <= 0 || $sideB <= 0 || $sideC <= 0) {
            throw new \InvalidArgumentException('Сторони мають бути додатними.');
        }

        if ($sideA + $sideB <= $sideC || $sideA + $sideC <= $sideB || $sideB + $sideC <= $sideA) {
            throw new \InvalidArgumentException('Трикутник з такими сторонами не існує.');
        }
    }

    public function perimeter(): float
    {
        return $this->sideA + $this->sideB + $this->sideC;
    }

    public function area(): float
    {
        $semi = $this->perimeter() / 2;

        return sqrt($semi * ($semi - $this->sideA) * ($semi - $this->sideB) * ($semi - $this->sideC));
    }
}
