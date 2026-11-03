<?php

declare(strict_types=1);

namespace App\Tasks;

final class Exam extends Test
{
    public function getPassingScore(): int
    {
        return parent::getPassingScore() + 10;
    }
}
