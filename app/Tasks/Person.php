<?php

declare(strict_types=1);

namespace App\Tasks;

/**
 * Табл. 4.3 №2 (варіант 6): базовий клас ієрархії персон.
 */
abstract class Person
{
    public function __construct(
        protected string $fullName,
    ) {}

    abstract public function getRoleDetails(): string;

    protected function baseRoleLine(string $role): string
    {
        return "{$this->fullName}: {$role}";
    }
}
