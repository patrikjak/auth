<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories;

use Illuminate\Support\Collection;
use Patrikjak\Auth\Exceptions\ModelIsIncompatibleException;
use Patrikjak\Auth\Factories\RoleFactory;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Repositories\Interfaces\RoleRepository as RoleRepositoryInterface;

final readonly class RoleRepository implements RoleRepositoryInterface
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

        $role = $roleModelClass::firstOrNew(['slug' => $slug]);

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
        return RoleFactory::getRoleModelClass()::all();
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function findBySlug(string $slug): ?Role
    {
        return RoleFactory::getRoleModelClass()::where('slug', $slug)->first();
    }

    /**
     * @throws ModelIsIncompatibleException
     */
    public function findById(string $id): ?Role
    {
        return RoleFactory::getRoleModelClass()::find($id);
    }
}
