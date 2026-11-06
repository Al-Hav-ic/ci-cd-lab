<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

use App\Core\Database;
use App\Core\EntityFactory;
use App\Domain\Product as CatalogProduct;
use App\Interfaces\ExportableInterface;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Tasks\DepartmentHead;
use App\Tasks\Product as StockProduct;
use App\Tasks\Store;
use App\Tasks\Student;
use App\Tasks\Teacher;
use App\Tasks\Triangle;

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<pre>';
}

$eol = PHP_EOL;

echo "--- Singleton ---{$eol}";
$db1 = Database::getInstance();
$db2 = Database::getInstance();
echo $db1 === $db2 ? "Екземпляр той самий{$eol}" : "Екземпляри різні{$eol}";
echo $db1->getConnectionInfo()."{$eol}{$eol}";

echo "--- Factory ---{$eol}";
$factory = new EntityFactory;

$product = $factory->createEntity('product', [
    'id' => '101',
    'createdAt' => '2026-11-01 10:00:00',
    'name' => 'Молоко «Галичина» 2.5 % 900 мл',
    'price' => 32.50,
    'stockQty' => 48,
    'manufacturer' => 'Галичина',
]);

$customer = $factory->createEntity('customer', [
    'id' => 'u-1',
    'createdAt' => '2026-10-15 09:30:00',
    'fullName' => 'Олена Ковальчук',
    'email' => 'olena@example.com',
    'role' => 'customer',
]);

echo $product->getDetails().$eol;
echo $customer->getDetails()."{$eol}{$eol}";

echo "--- JSON export ---{$eol}";
if ($product instanceof ExportableInterface) {
    echo $product->exportToJson()."{$eol}{$eol}";
}

echo "--- DI ---{$eol}";
$service = new EntityService(new ConsoleLogger);
$service->registerProduct($product);

if ($product instanceof CatalogProduct) {
    $sum = $service->calculateLineTotal($product, 3, 5.0);
    echo 'Сума до сплати: '.number_format($sum, 2)." грн{$eol}{$eol}";
}

echo "--- Варіант 6 (табл. 4.2 №1, 4.3 №2, 4.4 №2) ---{$eol}";

$store = new Store('wh-01', 'Склад супермаркету №1');
$store->addProduct(new StockProduct(1, 'Рис «Місто» 1 кг', 45.90));
$store->addProduct(new StockProduct(2, 'Олія соняшникова 1 л', 62.00));
$store->addProduct(new StockProduct(3, 'Молоко 2.5 % 900 мл', 32.50));

echo 'На складі «'.$store->getName().'»: '.count($store->getProducts())." позицій{$eol}";
$found = $store->searchProduct('молок');
echo 'Пошук «молок»: '.($found[0]->getName() ?? 'нічого').$eol;
$store->removeProduct(2);
echo 'Після removeProduct(2): '.count($store->getProducts())." позицій{$eol}";

foreach ([
    new Student('Іван Петренко', 'КН-21'),
    new Teacher('Марія Іваненко', 'КН-21', 'PHP'),
    new DepartmentHead('Олексій Гавриленко', 'КН-21', 'ТРПЗ', 'Програмна інженерія'),
] as $person) {
    echo $person->getRoleDetails().$eol;
}

$triangle = new Triangle(3.0, 4.0, 5.0);
echo 'Трикутник 3-4-5: периметр '.$triangle->perimeter()
    .', площа '.round($triangle->area(), 4).$eol;

if (PHP_SAPI !== 'cli') {
    echo '</pre>';
    echo '<p><a href="/lab3-demo.php">Демо лаб. 3</a></p>';
}
