<?php

namespace App\Notifications\Academy;

use Illuminate\Notifications\Messages\MailMessage;

class CertificateCollectedNotification extends AcademyApplicationNotification
{
    public function notificationType(): string
    {
        return 'academy.application.collected';
    }

    public function title(): string
    {
        return 'Certificate collection confirmed';
    }

    public function body(): string
    {
        return sprintf(
            'Your certificate for receipt %s has been collected. Thank you for completing the academy membership process.',
            $this->application->receipt_number
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your academy certificate collection has been recorded.'))
            ->line(__('Receipt: :receipt', ['receipt' => $this->application->receipt_number]))
            ->line(__('Thank you for completing your membership certification.'));
    }
}
