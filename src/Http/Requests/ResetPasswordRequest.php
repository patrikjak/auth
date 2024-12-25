<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Patrikjak\Utils\Common\Helpers\GrammaticalGender;
use Patrikjak\Utils\Common\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * @return array<string, array<string|Password>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [new Password(), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => trans_choice('pjutils::validation.required', GrammaticalGender::MASCULINE),
            'token.required' => trans_choice('pjutils::validation.required', GrammaticalGender::MASCULINE),
            'password.confirmed' => __('pjauth::validation.confirmed'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'token' => __('pjauth::forms.token'),
            'email' => __('pjauth::forms.email'),
            'password' => __('pjauth::forms.password'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getCredentials(): array
    {
        return $this->only('email', 'password', 'password_confirmation', 'token');
    }

    public function getNewPassword(): string
    {
        return $this->input('password');
    }
}
