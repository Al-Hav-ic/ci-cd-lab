<?php

declare(strict_types=1);

namespace App\Tasks;

class Teacher extends Student
{
    public function __construct(
        string $fullName,
        string $groupName,
        private string $subject,
    ) {
        parent::__construct($fullName, $groupName);
    }

    public function getRoleDetails(): string
    {
        $base = parent::getRoleDetails();

        return $base.'; викладає '.$this->subject;
    }
}
