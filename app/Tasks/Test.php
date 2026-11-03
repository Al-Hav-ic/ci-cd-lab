<?php

declare(strict_types=1);

namespace App\Tasks;

final class Test extends Trial
{
    public function getPassingScore(): int
    {
        return 50;
    }
}
