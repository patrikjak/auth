<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Repositories\Contracts;

use Illuminate\Support\Collection;
use Patrikjak\Auth\Models\Role;

interface RoleRepository
{
    public function create(string $slug, string $name, bool $isSuperadmin = false): void;

    public function firstOrCreate(string $slug, string $name, bool $isSuperadmin = false): Role;

    public function getAll(): Collection;

    public function findBySlug(string $slug): ?Role;

    public function findById(string $id): ?Role;
}
