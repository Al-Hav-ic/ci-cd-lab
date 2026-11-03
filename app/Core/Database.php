<?php

declare(strict_types=1);

namespace App\Core;

final class Database
{
    private static ?self $instance = null;

    private function __construct()
    {
        echo 'Підключення до БД створено'.PHP_EOL;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone(): void {}

    public function __wakeup(): void
    {
        throw new \RuntimeException('Десеріалізація Singleton заборонена.');
    }

    public function getConnectionInfo(): string
    {
        return 'PDO: mysql:host=localhost;dbname=shop (демо-підключення)';
    }
}
