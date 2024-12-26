<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CurrentPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Hash::check($value, Auth::user()->getAuthPassword())) {
            $fail(__('passwords.current'));
        }
    }
}
