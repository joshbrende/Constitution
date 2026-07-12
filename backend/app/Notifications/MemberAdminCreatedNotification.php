<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberAdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $loginUrl,
        public string $email,
        public string $temporaryPassword,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your ZANU PF member account'))
            ->greeting(__('Account created'))
            ->line(__('An administrator created a membership account for you on the ZANU PF Constitution platform.'))
            ->line(__('Email: :email', ['email' => $this->email]))
            ->line(__('Temporary password: :password', ['password' => $this->temporaryPassword]))
            ->line(__('Sign in and change your password. Complete certificate payment to receive your membership number.'))
            ->action(__('Sign in'), $this->loginUrl);
    }
}
