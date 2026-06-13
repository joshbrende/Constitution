<?php

namespace App\Notifications\Academy;

use App\Models\CertificateApplication;
use Illuminate\Notifications\Notification;

abstract class AcademyApplicationNotification extends Notification
{
    public function __construct(
        public CertificateApplication $application,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    abstract public function notificationType(): string;

    abstract public function title(): string;

    abstract public function body(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->notificationType(),
            'application_id' => $this->application->id,
            'title' => $this->title(),
            'body' => $this->body(),
            'receipt_number' => $this->application->receipt_number,
            'status' => $this->application->status->value,
            'status_label' => $this->application->status->label(),
        ];
    }

    protected function feeLine(): string
    {
        $amount = number_format((float) $this->application->fee_amount, 2);
        $currency = $this->application->fee_currency ?: 'USD';

        return sprintf('%s %s', $currency, $amount);
    }
}
