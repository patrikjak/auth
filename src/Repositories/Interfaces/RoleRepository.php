<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface RoleRepository
{
    public function create(int $id, string $name): void;

    public function getAll(): Collection;
}