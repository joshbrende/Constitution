<?php

namespace App\Notifications\Academy;

use Illuminate\Notifications\Messages\MailMessage;

class PaymentConfirmedNotification extends AcademyApplicationNotification
{
    public function notificationType(): string
    {
        return 'academy.application.payment_confirmed';
    }

    public function title(): string
    {
        return 'Payment received';
    }

    public function body(): string
    {
        return sprintf(
            'Your payment for receipt %s has been confirmed. Your certificate application is now awaiting Presidium approval.',
            $this->application->receipt_number
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your certificate fee payment has been confirmed.'))
            ->line(__('Receipt: :receipt', ['receipt' => $this->application->receipt_number]))
            ->line(__('Your application is now awaiting Presidium approval before your certificate can be printed.'))
            ->line(__('We will notify you when your certificate is ready for collection.'));
    }
}
