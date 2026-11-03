<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

use App\Core\Database;
use App\Core\EntityFactory;
use App\Interfaces\ExportableInterface;
use App\Models\Product;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Tasks\Exam;
use App\Tasks\FinalExam;
use App\Tasks\Group;
use App\Tasks\Rectangle;
use App\Tasks\Student;
use App\Tasks\Test;

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
    'name' => 'Ноутбук Lenovo IdeaPad 3',
    'price' => 24999.50,
    'stockQty' => 12,
    'manufacturer' => 'Lenovo',
]);

$customer = $factory->createEntity('customer', [
    'id' => 'u-1',
    'createdAt' => '2026-10-15 09:30:00',
    'fullName' => 'Олена Ковальчук',
    'email' => 'olena@example.com',
    'role' => 'manager',
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

if ($product instanceof Product) {
    $sum = $service->calculateLineTotal($product, 3, 5.0);
    echo 'Сума до сплати: '.number_format($sum, 2)." грн{$eol}{$eol}";
}

echo "--- Варіант 7 ---{$eol}";

$group = new Group('g-1', 'КН-21');
$group->addStudent(new Student('s-1', 'Іван', 'Петренко', '2005-03-14'));
$group->addStudent(new Student('s-2', 'Марія', 'Іваненко', '2004-11-02'));

echo 'У групі '.count($group->getStudents())." студентів{$eol}";
$found = $group->searchStudent('петр');
echo 'Пошук «петр»: '.($found[0]->getFullName() ?? 'нічого не знайдено').$eol;

foreach ([new Test('Поточний тест'), new Exam('Модульний іспит'), new FinalExam('Випускний іспит')] as $trial) {
    echo $trial->getTitle().': прохідний бал '.$trial->getPassingScore().$eol;
}

$rectangle = new Rectangle(4.0, 2.5);
echo 'Прямокутник 4×2.5: периметр '.$rectangle->perimeter()
    .', площа '.$rectangle->area().$eol;

if (PHP_SAPI !== 'cli') {
    echo '</pre>';
    echo '<p><a href="/lab3-demo.php">Демо лаб. 3 (Faker, масиви)</a></p>';
}
