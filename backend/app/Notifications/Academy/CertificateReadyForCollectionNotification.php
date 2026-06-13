<?php

namespace App\Notifications\Academy;

use Illuminate\Notifications\Messages\MailMessage;

class CertificateReadyForCollectionNotification extends AcademyApplicationNotification
{
    public function notificationType(): string
    {
        return 'academy.application.ready_for_collection';
    }

    public function title(): string
    {
        return 'Your certificate is ready for collection';
    }

    public function body(): string
    {
        $office = trim((string) ($this->application->collection_office ?? ''));
        $suffix = $office !== '' ? " at {$office}" : ' at the designated party office';

        return sprintf(
            'Your certificate for receipt %s is ready for collection%s. Please bring your national ID when collecting.',
            $this->application->receipt_number,
            $suffix
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your printed academy certificate is ready for collection.'))
            ->line(__('Receipt: :receipt', ['receipt' => $this->application->receipt_number]));

        if ($this->application->collection_office) {
            $message->line(__('Collection office: :office', [
                'office' => $this->application->collection_office,
            ]));
        }

        return $message
            ->line(__('Please bring your national ID when collecting your certificate.'))
            ->line(__('Certificates cannot be downloaded from the mobile app; they must be collected in person.'));
    }
}
