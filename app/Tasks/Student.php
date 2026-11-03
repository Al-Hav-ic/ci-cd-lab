<?php

declare(strict_types=1);

namespace App\Tasks;

final class Student
{
    public function __construct(
        private string $id,
        private string $name,
        private string $surname,
        private string $birthday,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->name.' '.$this->surname;
    }

    public function matches(string $query): bool
    {
        $query = mb_strtolower($query);

        return str_contains(mb_strtolower($this->name), $query)
            || str_contains(mb_strtolower($this->surname), $query);
    }
}
