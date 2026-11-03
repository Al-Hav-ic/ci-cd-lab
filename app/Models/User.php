<?php

declare(strict_types=1);

namespace App\Models;

class User extends AbstractEntity
{
    public function __construct(
        string $id,
        string $createdAt,
        private string $fullName,
        private string $email,
        private string $role = 'customer',
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return sprintf(
            'Користувач #%s: %s <%s>, роль — %s',
            $this->id,
            $this->fullName,
            $this->email,
            $this->role,
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
