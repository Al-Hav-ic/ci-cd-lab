<?php

declare(strict_types=1);

namespace App\Tasks;

abstract class Trial
{
    public function __construct(protected string $title) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    abstract public function getPassingScore(): int;
}
