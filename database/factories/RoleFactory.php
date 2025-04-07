<?php

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\RoleType;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $role = $this->faker->randomElement(RoleType::getAll());
        assert($role instanceof RoleType);

        return [
            'id' => $role->value,
            'name' => $role->name,
        ];
    }

    public function withRole(RoleType $roleType): Factory
    {
        return $this->state(static fn (array $attributes) => ['id' => $roleType->value, 'name' => $roleType->name]);
    }
}
