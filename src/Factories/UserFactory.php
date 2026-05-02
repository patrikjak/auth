<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Factories;

use Illuminate\Hashing\HashManager;
use Illuminate\Http\Request;
use Patrikjak\Auth\Models\User;

class UserFactory
{
    public static function createFromRequest(Request $request): User
    {
        /** @var HashManager $hashManager */
        $hashManager = app(HashManager::class);

        $userModel = self::getUserModelClass();

        $userModel = new $userModel();
        $userModel->name = $request->input('name');
        $userModel->email = $request->input('email');
        $userModel->password = $hashManager->make($request->input('password'));

        return $userModel;
    }

    public static function getUserModel(): User
    {
        $userModel = new (self::getUserModelClass())();
        assert($userModel instanceof User);

        return $userModel;
    }

    public static function getUserModelClass(): string
    {
        $userModel = config('auth.providers.users.model') ?? User::class;
        assert($userModel === User::class || is_subclass_of($userModel, User::class));

        return $userModel;
    }
}
