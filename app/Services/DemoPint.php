<?php

declare(strict_types=1);

namespace App\Services;

final class DemoPint
{
    public function run(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $out[]=$item;
        }

        return $out;
    }
}
