<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Models;

use Illuminate\Http\Request;

class UserFactory
{
    public static function createFromRequest(Request $request): User
    {
        $userModel = self::getUserModel();

        $userModel = new $userModel();
        $userModel->name = $request->input('name');
        $userModel->email = $request->input('email');
        $userModel->password = $request->input('password');

        return $userModel;
    }

    public static function getUserModel(): string
    {
        $userModel = config('pjauth.models.user') ?? User::class;
        assert($userModel === User::class || is_subclass_of($userModel, User::class));

        return $userModel;
    }
}