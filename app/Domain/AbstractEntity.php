<?php

declare(strict_types=1);

namespace App\Domain;

abstract class AbstractEntity
{
    public function __construct(
        protected string $id,
        protected string $createdAt,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    abstract public function getDetails(): string;
}
