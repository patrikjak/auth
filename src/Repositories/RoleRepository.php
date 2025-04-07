<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Support\Collection;
use Patrikjak\Auth\Exceptions\ModelIsIncompatibleException;
use Patrikjak\Auth\Factories\RoleFactory;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository as RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @throws ModelIsIncompatibleException
     */
    public function create(int $id, string $name): void
    {
        RoleFactory::getRoleModelClass()::create([
            'id' => $id,
            'name' => $name,
        ]);
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function getAll(): Collection
    {
        return RoleFactory::getRoleModelClass()::all();
    }
}