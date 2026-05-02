<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories\Implementations;

use Illuminate\Support\Collection;
use Patrikjak\Auth\Exceptions\ModelIsIncompatibleException;
use Patrikjak\Auth\Factories\RoleFactory;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository as RoleRepositoryInterface;

final readonly class EloquentRoleRepository implements RoleRepositoryInterface
{
    /**
     * @throws ModelIsIncompatibleException
     */
    public function create(string $slug, string $name, bool $isSuperadmin = false): void
    {
        $roleModelClass = RoleFactory::getRoleModelClass();

        $role = new $roleModelClass();

        $role->slug = $slug;
        $role->name = $name;
        $role->is_superadmin = $isSuperadmin;

        $role->save();
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function firstOrCreate(string $slug, string $name, bool $isSuperadmin = false): Role
    {
        $roleModelClass = RoleFactory::getRoleModelClass();

        /** @var Role $role */
        $role = $roleModelClass::query()->firstOrNew(['slug' => $slug]);

        if (!$role->exists) {
            $role->name = $name;
            $role->is_superadmin = $isSuperadmin;
            $role->save();
        }

        return $role;
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function getAll(): Collection
    {
        return RoleFactory::getRoleModelClass()::query()->get();
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function findBySlug(string $slug): ?Role
    {
        $roleModelClass = RoleFactory::getRoleModelClass();

        /** @var ?Role $role */
        $role = $roleModelClass::query()->where('slug', $slug)->first();

        return $role;
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function findById(string $id): ?Role
    {
        $roleModelClass = RoleFactory::getRoleModelClass();

        /** @var ?Role $role */
        $role = $roleModelClass::query()->find($id);

        return $role;
    }
}
