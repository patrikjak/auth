<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

class InviteRegisterRequest extends RegisterRequest
{
    public function getToken(): string
    {
        return $this->input('token', '');
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }
}
