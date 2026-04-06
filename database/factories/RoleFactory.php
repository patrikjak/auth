<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Patrikjak\Auth\Models\Role;

class RoleFactory extends Factory
{
    /**
     * @var class-string<Role>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $model = Role::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = $this->faker->unique()->slug(1);

        return [
            'slug' => $slug,
            'name' => ucfirst($slug),
            'is_superadmin' => false,
        ];
    }

    public function withSlug(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => $slug,
            'name' => ucfirst($slug),
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => 'superadmin',
            'name' => 'Superadmin',
            'is_superadmin' => true,
        ]);
    }
}
