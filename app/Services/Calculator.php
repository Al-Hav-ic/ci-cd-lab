<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Сервіс арифметичних операцій.
 *
 * Навмисне порушення форматування в add() використовується для демонстрації
 * роботи лінтера Laravel Pint у CI-пайплайні.
 */
class Calculator
{
    public function add(float $x, float $y): float
    {
        return $x+$y;
    }

    public function subtract(float $x, float $y): float
    {
        return $x - $y;
    }
}
