<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Patrikjak\Auth\Models\User;
use Patrikjak\Auth\Models\UserFactory;
use Patrikjak\Utils\Common\Helpers\GrammaticalGender;
use Patrikjak\Utils\Common\Http\Requests\Traits\ValidationException;
use Patrikjak\Utils\Common\Rules\Password;

class RegisterRequest extends FormRequest
{
    use ValidationException;

    /**
     * @return array<string, array<string|Password>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => [new Password()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => trans_choice('pjutils::validation.required', GrammaticalGender::NEUTER),
            'name.string' => __('pjauth::validation.string'),
            'name.max' => __('pjutils::validation.max.string'),
            'email.required' => trans_choice('pjutils::validation.required', GrammaticalGender::MASCULINE),
            'email.string' => __('pjauth::validation.string'),
            'email.email' => __('pjauth::validation.email'),
            'email.unique' => trans_choice('pjutils::validation.unique', GrammaticalGender::MASCULINE),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('pjauth::forms.name'),
            'email' => __('pjauth::forms.email'),
            'password' => __('pjauth::forms.password'),
        ];
    }

    public function getNewUser(): User
    {
        return UserFactory::createFromRequest($this);
    }
}
