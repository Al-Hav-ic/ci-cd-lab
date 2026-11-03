<?php

declare(strict_types=1);

namespace App\Tasks;

final class DepartmentHead extends Teacher
{
    public function __construct(
        string $fullName,
        string $groupName,
        string $subject,
        private string $departmentName,
    ) {
        parent::__construct($fullName, $groupName, $subject);
    }

    public function getRoleDetails(): string
    {
        $base = parent::getRoleDetails();

        return $base.'; завідувач кафедри «'.$this->departmentName.'»';
    }
}
