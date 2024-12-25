<?php

namespace Patrikjak\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public function __construct(private readonly string $resetPasswordUrl)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->view(
            ['pjauth::notifications.html.password-reset', 'pjauth::notifications.text.password-reset'],
            [
                'resetUrl' => $this->resetPasswordUrl,
                'expireIn' => config('auth.passwords.users.expire'),
            ],
        )->subject(__('pjauth::notifications.reset_password.subject'));
    }

    public function toArray($notifiable): array
    {
        return [
            'resetUrl' => $this->resetPasswordUrl,
            'expireIn' => config('auth.passwords.users.expire'),
        ];
    }
}
