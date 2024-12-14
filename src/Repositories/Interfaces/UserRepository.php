<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Repositories\Interfaces;

use Patrikjak\Auth\Dto\CreateUserInterface;
use Patrikjak\Auth\Models\User;

interface UserRepository
{
    public function createAndReturnUser(CreateUserInterface $user): User;
}