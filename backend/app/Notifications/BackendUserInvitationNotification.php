<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;

class BackendUserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * @param  list<array{slug: string, name: string, title: string, summary: string, steps: list<string>, sections: list<string>}>  $dutyBriefs
     */
    public function __construct(
        public string $acceptUrl,
        public string $loginUrl,
        public string $email,
        public string $roleSummary,
        public array $dutyBriefs,
        public string $dutyText,
    ) {
        $this->onQueue('mail');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('Welcome — backend dashboard access'))
            ->greeting(__('Hello,'))
            ->line(__('You have been invited to the ZANU PF administration dashboard with the following role(s): :roles', [
                'roles' => $this->roleSummary,
            ]))
            ->line(__('Sign-in email: :email', ['email' => $this->email]))
            ->line(__('Dashboard login: :url', ['url' => $this->loginUrl]))
            ->line(__('Your assigned duties (you do not have access to other admin areas):'));

        foreach ($this->dutyBriefs as $brief) {
            $message->line($brief['title'].' — '.$brief['summary']);
            if ($brief['sections'] !== []) {
                $message->line(__('Areas: :areas', ['areas' => implode(', ', $brief['sections'])]));
            }
        }

        return $message
            ->action(__('Activate account & set password'), $this->acceptUrl)
            ->line(__('Use the button above to choose your name and password. This link expires in 7 days.'))
            ->line(__('If you did not expect this message, please contact your system administrator.'));
    }
}
