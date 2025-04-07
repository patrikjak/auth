<?php

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\RoleType;
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

        $this->model = \Patrikjak\Auth\Factories\UserFactory::getUserModelClass();
    }

    public function definition(): array
    {
        $this->seedRoles();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password()),
            'remember_token' => Str::random(10),
            'role_id' => RoleType::USER->value,
        ];
    }

    public function withName(string $name): Factory
    {
        return $this->state(fn (array $attributes) => ['name' => $name]);
    }

    public function withEmail(string $email): Factory
    {
        return $this->state(fn (array $attributes) => ['email' => $email]);
    }

    public function withPassword(string $password): Factory
    {
        return $this->state(fn (array $attributes) => ['password' => bcrypt($password)]);
    }

    public function withGoogleId(string $googleId): Factory
    {
        return $this->state(fn (array $attributes) => ['google_id' => $googleId]);
    }

    public function withRole(RoleType $roleType): Factory
    {
        return $this->state(fn (array $attributes) => ['role_id' => $roleType->value]);
    }

    private function seedRoles(): void
    {
        if (Role::count() > 0) {
            return;
        }

        foreach (RoleType::getAll() as $roleType) {
            Role::factory()->create([
                'id' => $roleType->value,
                'name' => $roleType->name,
            ]);
        }
    }
}
