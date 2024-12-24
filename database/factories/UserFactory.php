<?php

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password()),
            'remember_token' => Str::random(10),
            'role_id' => Role::factory(),
        ];
    }

    public function withGoogleId(string $googleId): Factory
    {
        return $this->state(static fn (array $attributes) => ['google_id' => $googleId]);
    }
}
