<?php

declare(strict_types=1);

namespace Patrikjak\Auth\Http\Requests;

class InviteRegisterRequest extends RegisterRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'token' => ['required', 'string'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'token.required' => __('pjauth::validation.token_required'),
        ]);
    }

    public function getToken(): string
    {
        return $this->input('token');
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }
}
