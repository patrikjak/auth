<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Support\Collection;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository as RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function create(int $id, string $name): void
    {
        Role::create([
            'id' => $id,
            'name' => $name,
        ]);
    }

    public function getAll(): Collection
    {
        return Role::all();
    }
}