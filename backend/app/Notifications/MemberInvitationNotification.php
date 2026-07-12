<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $acceptUrl,
        public string $email,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Invitation to ZANU PF membership'))
            ->greeting(__('You have been invited'))
            ->line(__('You have been invited to join the ZANU PF Constitution platform as a member. You will skip the academy exam and complete certificate payment to finalise membership.'))
            ->action(__('Accept invitation'), $this->acceptUrl)
            ->line(__('This link expires in 7 days. If you did not expect this email, you can ignore it.'));
    }
}
