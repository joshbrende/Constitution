<?php

namespace App\Notifications\Academy;

use App\Services\AcademyPaymentOfficeService;
use Illuminate\Notifications\Messages\MailMessage;

class ExamPassedPaymentRequiredNotification extends AcademyApplicationNotification
{
    public function notificationType(): string
    {
        return 'academy.application.payment_pending';
    }

    public function title(): string
    {
        return 'Exam passed – payment required';
    }

    public function body(): string
    {
        $courseTitle = $this->application->course?->title ?? 'your course';

        return sprintf(
            'Congratulations! You passed %s. A payment receipt has been issued. Pay %s at the designated government office using receipt %s and reference code %s.',
            $courseTitle,
            $this->feeLine(),
            $this->application->receipt_number,
            $this->application->payment_reference_code
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing(['course', 'user']);
        $instructions = app(AcademyPaymentOfficeService::class)->paymentInstructions($this->application);

        return (new MailMessage)
            ->subject($this->title())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Congratulations! You passed your academy assessment.'))
            ->line(__('Course: :course', ['course' => $this->application->course?->title ?? '—']))
            ->line(__('Amount due: :amount', ['amount' => $this->feeLine()]))
            ->line(__('Receipt number: :receipt', ['receipt' => $this->application->receipt_number]))
            ->line(__('Payment reference code: :code', ['code' => $this->application->payment_reference_code]))
            ->line(__('Instructions: :instructions', ['instructions' => $instructions]))
            ->line(__('Open the Academy portal in the app to download your payment receipt.'));
    }
}
