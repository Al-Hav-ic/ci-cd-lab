<?php

declare(strict_types=1);

use Faker\Factory;

$base = dirname(__DIR__);

require_once $base.'/vendor/autoload.php';
require_once $base.'/app/Services/EntityFunctions.php';
require_once $base.'/app/Services/DataProcessor.php';

$faker = Factory::create('uk_UA');
$faker->seed(42);

$prices = [];
for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->randomFloat(2, 150, 5000);
}

$minThreshold = 1000.0;

$expensive = array_filter($prices, function (float $price) use ($minThreshold): bool {
    return $price > $minThreshold;
});

$expensive = array_values($expensive);

$lineTotal = calculateLineTotal(32.50, 3, 5.0);

$numbers = [42, 7, 105, 3, 88, 19, 64];
$sentence = 'перше речення містить кілька слів для демонстрації';
$wordsForCount = 'ма milk aa bbccc dd ee';

$sorted = sortDescending($numbers);
$swapped = swapFirstLastWord($sentence);
$evenWordCount = countWordsWithEvenLength($wordsForCount);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лабораторна 3 — функції, Faker, винятки</title>
</head>
<body>
<h1>Функції, замикання, винятки</h1>
<p><a href="/lab3-entity-array.php">Блок 1: масиви (EntityArray)</a></p>

<h2>Згенеровані ціни (Faker)</h2>
<ul>
    <?php foreach ($prices as $price) { ?>
        <li><?= number_format($price, 2, '.', ' ') ?> грн</li>
    <?php } ?>
</ul>

<h2>Дорожчі за <?= number_format($minThreshold, 2) ?> грн (array_filter + use)</h2>
<ul>
    <?php foreach ($expensive as $price) { ?>
        <li><?= number_format($price, 2, '.', ' ') ?> грн</li>
    <?php } ?>
</ul>
<p>Знайдено позицій: <?= count($expensive) ?> з <?= count($prices) ?></p>

<h2>Розрахунок вартості (строга типізація)</h2>
<p>3 × 32.50 грн зі знижкою 5 % = <?= number_format($lineTotal, 2, '.', ' ') ?> грн</p>

<h2>Обробка винятку</h2>
<?php
try {
    $discounted = applyDiscount(1000.0, 150.0);
    echo '<p>Ціна зі знижкою: '.number_format($discounted, 2).' грн</p>';
} catch (Exception $e) {
    echo '<p>Помилка: '.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</p>';
} finally {
    echo '<p>Завершення спроби розрахунку.</p>';
}

try {
    $discounted = applyDiscount(1000.0, 10.0);
    echo '<p>Коректний виклик: 1000 грн − 10 % = '.number_format($discounted, 2).' грн</p>';
} catch (Exception $e) {
    echo '<p>Помилка: '.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</p>';
}
?>

<h2>Алгоритмічні задачі (варіант 6: табл. 3.2 №3, 3.3 №4, 3.4 №5)</h2>
<p>Масив: <?= implode(', ', $numbers) ?></p>
<p>За спаданням (3.2 №3): <?= implode(', ', $sorted) ?></p>
<p>Перше ↔ останнє слово (3.3 №4): «<?= htmlspecialchars($sentence, ENT_QUOTES, 'UTF-8') ?>» → «<?= htmlspecialchars($swapped, ENT_QUOTES, 'UTF-8') ?>»</p>
<p>Слів із парною довжиною (3.4 №5) у «<?= htmlspecialchars($wordsForCount, ENT_QUOTES, 'UTF-8') ?>»: <?= $evenWordCount ?></p>
</body>
</html>
