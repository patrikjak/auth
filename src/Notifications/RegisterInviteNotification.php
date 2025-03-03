<?php

declare(strict_types = 1);

namespace Patrikjak\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegisterInviteNotification extends Notification
{
    public function __construct(private readonly string $registerUrl)
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
        return new MailMessage()->view(
            [
                'pjauth::notifications.html.register-invite',
                'pjauth::notifications.text.register-invite',
            ],
            [
                'registerUrl' => $this->registerUrl,
            ],
        )->subject(__('pjauth::notifications.register_invite.subject'));
    }
}
