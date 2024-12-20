<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

use Patrikjak\Auth\Models\User;

interface UserRepository
{
    public function createAndReturnUser(User $user): User;
}