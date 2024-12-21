<?php

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Patrikjak\Auth\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->randomKey(['SUPERADMIN', 'ADMIN', 'USER']),
        ];
    }
}
