<?php

declare(strict_types=1);
use Faker\Factory;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/app/Services/EntityFunctions.php';
require_once __DIR__.'/app/Services/DataProcessor.php';

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

$lineTotal = calculateLineTotal(24999.50, 2, 5.0);

$numbers = [42, 7, 105, 3, 88, 19, 64];
$text = 'Це речення містить слова різної довжини для перевірки алгоритму';
$mixed = 'abc123def45';

$sorted = sortDescending($numbers);
$shortened = removeWordsOfLength($text, 3, 5);
$reordered = digitsFirst($mixed);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лабораторна 3 — функції, Faker, винятки</title>
</head>
<body>
<h1>Функції, замикання, винятки</h1>
<p><a href="/app/Models/EntityArray.php">Блок 1: масиви (EntityArray.php)</a></p>

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
<p>2 × 24 999.50 грн зі знижкою 5 % = <?= number_format($lineTotal, 2, '.', ' ') ?> грн</p>

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

<h2>Алгоритмічні задачі (варіант 7)</h2>
<p>Масив: <?= implode(', ', $numbers) ?></p>
<p>За спаданням: <?= implode(', ', $sorted) ?></p>
<p>Без слів довжиною 3–5: <?= htmlspecialchars($shortened, ENT_QUOTES, 'UTF-8') ?></p>
<p>«<?= htmlspecialchars($mixed, ENT_QUOTES, 'UTF-8') ?>» → «<?= htmlspecialchars($reordered, ENT_QUOTES, 'UTF-8') ?>»</p>
</body>
</html>
