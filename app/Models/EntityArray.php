<?php

declare(strict_types=1);

$categories = [
    'М\'ясо та риба',
    'Молочні продукти',
    'Напої',
    'Овочі та фрукти',
    'Бакалія',
];

sort($categories, SORT_STRING);

$product = [
    'id' => 101,
    'sku' => 'MLK-900-25',
    'name' => 'Молоко «Галичина» 2.5 % 900 мл',
    'price' => 32.50,
    'stock_qty' => 48,
    'manufacturer' => 'Галичина',
    'is_active' => true,
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лабораторна 3 — структури даних</title>
</head>
<body>
<h1>Структури даних</h1>

<h2>Категорії (за алфавітом)</h2>
<ul>
    <?php foreach ($categories as $category) { ?>
        <li><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></li>
    <?php } ?>
</ul>

<h2>Товар</h2>
<ul>
    <?php foreach ($product as $key => $value) { ?>
        <li>
            <strong><?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?>:</strong>
            <?= htmlspecialchars(is_bool($value) ? ($value ? 'так' : 'ні') : (string) $value, ENT_QUOTES, 'UTF-8') ?>
        </li>
    <?php } ?>
</ul>

<p><a href="/lab3-demo.php">← Демо лаб. 3 (Faker, алгоритми)</a></p>
</body>
</html>
