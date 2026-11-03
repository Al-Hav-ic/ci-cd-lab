<?php

declare(strict_types=1);

namespace App\Tasks;

class Student extends Person
{
    public function __construct(
        string $fullName,
        private string $groupName,
    ) {
        parent::__construct($fullName);
    }

    public function getRoleDetails(): string
    {
        return parent::baseRoleLine('студент').", група {$this->groupName}";
    }
}
