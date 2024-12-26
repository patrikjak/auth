<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Patrikjak\Auth\Rules\CurrentPassword;
use Patrikjak\Utils\Common\Helpers\GrammaticalGender;
use Patrikjak\Utils\Common\Http\Requests\Traits\ValidationException;
use Patrikjak\Utils\Common\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    use ValidationException;

    /**
     * @return array<string, array<string|object>>
     */
    public function rules(): array
    {
        $rules = [
            'password' => [new Password(), 'confirmed'],
        ];

        if ($this->requireCurrentPasswordValidation()) {
            $rules['current_password'] = ['required', new CurrentPassword()];
        }

        return $rules;
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => trans_choice('pjutils::validation.required', GrammaticalGender::NEUTER),
            'password.confirmed' => __('pjauth::validation.confirmed'),
        ];
    }

    /**
     * @return array<string>
     */
    public function attributes(): array
    {
        return [
            'current_password' => __('pjauth::forms.current_password'),
            'password' => __('pjauth::forms.password'),
        ];
    }

    public function getNewPassword(): string
    {
        return $this->input('password');
    }

    public function getUserId(): string
    {
        return $this->user()->getAuthIdentifier();
    }

    private function requireCurrentPasswordValidation(): bool
    {
        return $this->boolean('validate_current_password', true);
    }
}
