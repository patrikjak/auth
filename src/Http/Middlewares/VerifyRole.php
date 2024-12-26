<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Patrikjak\Auth\Models\RoleType;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Interfaces\UserRepository;

class VerifyRole
{
    public function handle(Request $request, Closure $next, int $role): mixed
    {
        $roleEnum = RoleType::tryFrom($role);

        if ($roleEnum === null) {
            abort(403);
        }

        $user = $request->user();
        assert($user instanceof User);

        if (!$user->hasRole($roleEnum)) {
            abort(403);
        }

        return $next($request);
    }
}
