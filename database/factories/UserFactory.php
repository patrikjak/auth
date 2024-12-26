<?php

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null,
        ?Collection $recycle = null,
        bool $expandRelationships = true
    ) {
        parent::__construct(
            $count,
            $states,
            $has,
            $for,
            $afterMaking,
            $afterCreating,
            $connection,
            $recycle,
            $expandRelationships
        );

        $this->model = \Patrikjak\Auth\Models\UserFactory::getUserModelClass();
    }

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
