<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Patrikjak\Auth\Exceptions\UnauthenticatedException;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Repositories\Contracts\RoleRepository;
use Symfony\Component\HttpFoundation\Response;

class VerifyRole
{
    public function __construct(private readonly RoleRepository $roleRepository)
    {
    }

    public function handle(Request $request, Closure $next, string $slug): mixed
    {
        $role = $this->roleRepository->findBySlug($slug);

        if ($role === null) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $user = $request->user();

        if (!$user instanceof User) {
            throw new UnauthenticatedException();
        }

        if ($user->role->slug !== $slug && !$user->role->is_superadmin) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    public static function withRole(string $slug): string
    {
        return sprintf('%s:%s', self::class, $slug);
    }
}
