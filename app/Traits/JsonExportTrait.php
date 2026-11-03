<?php

declare(strict_types=1);

namespace App\Traits;

trait JsonExportTrait
{
    public function exportToJson(): string
    {
        $data = get_object_vars($this);

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($json === false) {
            throw new \RuntimeException('Не вдалося серіалізувати об’єкт: '.json_last_error_msg());
        }

        return $json;
    }
}
