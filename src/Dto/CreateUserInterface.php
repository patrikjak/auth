<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Dto;

interface CreateUserInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getFillable(): array;
}