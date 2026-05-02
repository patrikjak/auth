<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Patrikjak\Auth\Models\Role;
use Patrikjak\Auth\Models\User;

class UserFactory extends Factory
{
    /**
     * @var class-string<User>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
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

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $defaultRoleId = $this->getOrCreateDefaultRole()->id;

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt($this->faker->password()),
            'remember_token' => Str::random(10),
            'role_id' => $defaultRoleId,
        ];
    }

    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => ['name' => $name]);
    }

    public function withEmail(string $email): static
    {
        return $this->state(fn (array $attributes) => ['email' => $email]);
    }

    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => ['password' => bcrypt($password)]);
    }

    public function withGoogleId(string $googleId): static
    {
        return $this->state(fn (array $attributes) => ['google_id' => $googleId]);
    }

    public function withRole(string $slug): static
    {
        return $this->state(function (array $attributes) use ($slug): array {
            $role = Role::where('slug', $slug)->first()
                ?? Role::factory()->withSlug($slug)->create();

            return ['role_id' => $role->id];
        });
    }

    private function getOrCreateDefaultRole(): Role
    {
        $defaultSlug = config('pjauth.default_role_slug', 'admin');

        $role = Role::where('slug', $defaultSlug)->first();

        if ($role !== null) {
            return $role;
        }

        return Role::factory()->withSlug($defaultSlug)->create();
    }
}
