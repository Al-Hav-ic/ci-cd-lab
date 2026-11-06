<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Доменна (не Eloquent) модель користувача з лабораторних №3–4.
 *
 * Eloquent-модель для роботи з БД — App\Models\Customer (лабораторна №6).
 */
class Customer extends AbstractEntity
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
            'Користувач #%s: %s <%s>, роль - %s',
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
