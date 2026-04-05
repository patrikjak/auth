<?php

declare(strict_types=1);

namespace Patrikjak\Auth\ValueObjects;

final readonly class RegisterInvite
{
    public function __construct(
        public string $token,
        public ?int $roleId,
    ) {
    }
}
