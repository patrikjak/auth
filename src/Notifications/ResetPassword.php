<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public function __construct(private readonly string $resetPasswordUrl)
    {
    }

    /**
     * @return array<string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage())->view(
            ['pjauth::notifications.html.password-reset', 'pjauth::notifications.text.password-reset'],
            [
                'resetUrl' => $this->resetPasswordUrl,
                'expireIn' => config('auth.passwords.users.expire'),
            ],
        )->subject(__('pjauth::notifications.reset_password.subject'));
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'resetUrl' => $this->resetPasswordUrl,
            'expireIn' => config('auth.passwords.users.expire'),
        ];
    }
}
