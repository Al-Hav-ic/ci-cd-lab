<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Сервіс арифметичних операцій.
 *
 * Форматування відповідає PSR-12 і перевіряється лінтером Laravel Pint
 * у CI-пайплайні (крок "Lint code with Pint").
 */
class Calculator
{
    public function add(float $x, float $y): float
    {
        return $x + $y;
    }

    public function subtract(float $x, float $y): float
    {
        return $x - $y;
    }
}
