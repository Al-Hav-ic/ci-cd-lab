<?php

declare(strict_types=1);

/**
 * Процедурні функції лаб. 3 (методичка: app/Services/EntityService.php).
 * Файл названо EntityFunctions.php, щоб не конфліктувати з класом EntityService (лаб. 4).
 */
function calculateLineTotal(float $price, int $quantity, float $discountPercent = 0.0): float
{
    if ($price < 0) {
        throw new Exception('Ціна не може бути від’ємною.');
    }

    if ($quantity < 1) {
        throw new Exception('Кількість має бути не меншою за 1.');
    }

    if ($discountPercent < 0 || $discountPercent > 100) {
        throw new Exception('Неприпустиме значення знижки!');
    }

    $total = $price * $quantity;

    return round($total - $total * $discountPercent / 100, 2);
}

/**
 * Знижка з перевіркою діапазону 0..100.
 */
function applyDiscount(float $price, float $discount): float
{
    if ($discount < 0 || $discount > 100) {
        throw new Exception('Неприпустиме значення знижки!');
    }

    return round($price - ($price * ($discount / 100)), 2);
}
