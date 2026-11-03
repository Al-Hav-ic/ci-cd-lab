<?php

declare(strict_types=1);

/**
 * Табл. 3.2, № 3: впорядкувати цілочисловий масив за спаданням.
 *
 * @param  array<int, int|float>  $numbers
 * @return array<int, int|float>
 */
function sortDescending(array $numbers): array
{
    if ($numbers === []) {
        return [];
    }

    $sorted = array_values($numbers);
    rsort($sorted);

    return $sorted;
}

/**
 * Табл. 3.3, № 2: видалити слова довжиною від $minLength до $maxLength.
 */
function removeWordsOfLength(string $text, int $minLength, int $maxLength): string
{
    if ($minLength < 1 || $maxLength < $minLength) {
        throw new InvalidArgumentException('Некоректний діапазон довжин слів.');
    }

    $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    $filtered = array_filter(
        $words,
        static fn (string $word): bool => mb_strlen($word, 'UTF-8') < $minLength
            || mb_strlen($word, 'UTF-8') > $maxLength
    );

    return implode(' ', $filtered);
}

/**
 * Табл. 3.4, № 7: спочатку цифри, потім літери, інші символи — в кінці.
 */
function digitsFirst(string $input): string
{
    $digits = '';
    $letters = '';
    $others = '';

    foreach (mb_str_split($input, 1, 'UTF-8') as $char) {
        if (preg_match('/^\d$/u', $char) === 1) {
            $digits .= $char;
        } elseif (preg_match('/^\p{L}$/u', $char) === 1) {
            $letters .= $char;
        } else {
            $others .= $char;
        }
    }

    return $digits.$letters.$others;
}
