<?php

namespace App\Notifications\Academy;

use Illuminate\Notifications\Messages\MailMessage;

class CertificatePresidiumApprovedNotification extends AcademyApplicationNotification
{
    public function notificationType(): string
    {
        return 'academy.application.presidium_approved';
    }

    public function title(): string
    {
        return 'Certificate approved for printing';
    }

    public function body(): string
    {
        return sprintf(
            'Presidium has approved your certificate for receipt %s. Your certificate is being prepared for printing and collection.',
            $this->application->receipt_number
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing('certificate');

        $message = (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your academy certificate has been approved by the Presidium.'))
            ->line(__('Receipt: :receipt', ['receipt' => $this->application->receipt_number]));

        if ($this->application->certificate?->certificate_number) {
            $message->line(__('Certificate number: :number', [
                'number' => $this->application->certificate->certificate_number,
            ]));
        }

        return $message->line(__('You will receive another message when your certificate is ready for collection at the party office.'));
    }
}
