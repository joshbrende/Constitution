<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackendUserWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<array{slug: string, name: string, title: string, summary: string, steps: list<string>, sections: list<string>}>  $dutyBriefs
     */
    public function __construct(
        public string $loginUrl,
        public string $email,
        public string $plainPassword,
        public string $roleSummary,
        public array $dutyBriefs,
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
            ->subject(__('Welcome — your backend dashboard account'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your backend account has been created with role(s): :roles', [
                'roles' => $this->roleSummary,
            ]))
            ->line(__('Email: :email', ['email' => $this->email]))
            ->line(__('Temporary password: :password', ['password' => $this->plainPassword]))
            ->line(__('Login: :url', ['url' => $this->loginUrl]))
            ->line(__('Change your password after first sign-in (Profile → security when available).'))
            ->line(__('Your assigned duties (other admin areas are not available to you):'));

        foreach ($this->dutyBriefs as $brief) {
            $message->line($brief['title'].' — '.$brief['summary']);
            if ($brief['sections'] !== []) {
                $message->line(__('Areas: :areas', ['areas' => implode(', ', $brief['sections'])]));
            }
        }

        return $message
            ->action(__('Sign in now'), $this->loginUrl)
            ->line(__('Keep this email confidential. If you did not expect access, contact your system administrator immediately.'));
    }
}
