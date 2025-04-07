<?php

namespace Patrikjak\Auth\Factories;

use Patrikjak\Auth\Exceptions\ModelIsIncompatibleException;
use Patrikjak\Auth\Models\Role;

class RoleFactory
{
    /**
     * @throws ModelIsIncompatibleException
     */
    public static function getRoleModelClass(): string
    {
        $roleModel = config('pjauth.models.role') ?? Role::class;

        if ($roleModel !== Role::class && !is_subclass_of($roleModel, Role::class)) {
            throw new ModelIsIncompatibleException($roleModel, Role::class);
        }

        return $roleModel;
    }
}