<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Patrikjak\Utils\Common\Helpers\GrammaticalGender;
use Patrikjak\Utils\Common\Http\Requests\Traits\ValidationException;

class LoginRequest extends FormRequest
{
    use ValidationException;

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => trans_choice('pjutils::validation.required', GrammaticalGender::MASCULINE),
            'password.required' => trans_choice('pjutils::validation.required', GrammaticalGender::NEUTER),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('forms.email'),
            'password' => __('forms.password'),
        ];
    }

    /**
     * @throws LaravelValidationException|BindingResolutionException
     */
    public function ensureIsNotRateLimited(): void
    {
        $rateLimiter = app()->make(RateLimiter::class);

        if (!$rateLimiter->tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = $rateLimiter->availableIn($this->throttleKey());

        throw LaravelValidationException::withMessages([
            'email' => __('pjauth::validation.throttle', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->getEmail()) . '|' . $this->ip());
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function getPassword(): string
    {
        return $this->input('password');
    }

    public function shouldRemember(): bool
    {
        return $this->boolean('remember');
    }
}
