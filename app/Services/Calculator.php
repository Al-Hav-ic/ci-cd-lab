<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

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

    public function multiply(float $x, float $y): float
    {
        return $x * $y;
    }

    /**
     * @throws InvalidArgumentException якщо дільник дорівнює нулю
     */
    public function divide(float $x, float $y): float
    {
        if ($y === 0.0) {
            throw new InvalidArgumentException('Division by zero is not allowed.');
        }

        return $x / $y;
    }
}
