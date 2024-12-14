<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

interface RoleRepository
{
    public function create(int $id, string $name): void;
}