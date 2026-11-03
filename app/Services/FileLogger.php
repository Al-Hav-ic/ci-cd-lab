<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LoggerInterface;

final class FileLogger implements LoggerInterface
{
    public function __construct(
        private string $filePath = __DIR__.'/../../storage/logs/app.log',
    ) {}

    public function log(string $message): void
    {
        $directory = dirname($this->filePath);

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Не вдалося створити каталог логів: '.$directory);
        }

        $line = '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL;

        if (file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new \RuntimeException('Не вдалося записати лог у '.$this->filePath);
        }
    }
}
