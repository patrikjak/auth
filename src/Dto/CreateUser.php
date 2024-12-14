<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Dto;

use Illuminate\Support\Facades\Hash;
use Patrikjak\Auth\Models\RoleType;

final readonly class CreateUser implements CreateUserInterface
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public RoleType $roleType = RoleType::USER,
        public ?string $googleId = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFillable(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $this->roleType->value,
        ];
    }
}