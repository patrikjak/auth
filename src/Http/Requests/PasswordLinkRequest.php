<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Patrikjak\Utils\Common\Helpers\GrammaticalGender;
use Patrikjak\Utils\Common\Http\Requests\Traits\ValidationException;

class PasswordLinkRequest extends FormRequest
{
    use ValidationException;

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return ['email' => ['required', 'email']];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => trans_choice('pjutils::validation.required', GrammaticalGender::MASCULINE),
            'email.email' => __('pjauth::validation.email'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['email' => __('pjauth::forms.email')];
    }

    /**
     * @return array<string, string>
     */
    public function getCredentials(): array
    {
        return $this->only('email');
    }
}
