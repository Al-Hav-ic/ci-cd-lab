<?php

declare(strict_types=1);

/**
 * Варіант 6 — табл. 3.2, № 3: впорядкувати цілочисловий масив за спаданням.
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
 * Варіант 6 — табл. 3.3, № 4: поміняти місцями перше і останнє слово.
 */
function swapFirstLastWord(string $text): string
{
    $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    if (count($words) < 2) {
        return trim($text);
    }

    $lastIndex = count($words) - 1;
    [$words[0], $words[$lastIndex]] = [$words[$lastIndex], $words[0]];

    return implode(' ', $words);
}

/**
 * Варіант 6 — табл. 3.4, № 5: кількість слів із парною кількістю символів.
 */
function countWordsWithEvenLength(string $text): int
{
    $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $count = 0;

    foreach ($words as $word) {
        if (mb_strlen($word, 'UTF-8') % 2 === 0) {
            $count++;
        }
    }

    return $count;
}
