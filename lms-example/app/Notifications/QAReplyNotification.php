<?php

namespace App\Notifications;

use App\Models\FacilitatorChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QAReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FacilitatorChatMessage $reply,
        public FacilitatorChatMessage $parent
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->reply->load(['course', 'user:id,name,surname']);
        $course = $this->reply->course;
        $replierName = $this->reply->user ? trim(($this->reply->user->name ?? '') . ' ' . ($this->reply->user->surname ?? '')) : 'The facilitator';
        if ($replierName === '') {
            $replierName = 'The facilitator';
        }
        $title = $course?->title ?? 'Course';
        $unitId = $this->reply->unit_id;
        $url = $course
            ? route('learn.show', [$course], $unitId ? ['unit' => $unitId] : [])
            : url('/');

        return (new MailMessage)
            ->subject('New reply to your question')
            ->line("Your question in «{$title}» has a new reply from {$replierName}.")
            ->action('View reply', $url)
            ->line('Thank you for using ' . config('app.name') . '.');
    }

    public function toArray(object $notifiable): array
    {
        $reply = $this->reply;
        $reply->load(['course', 'user:id,name,surname']);
        $course = $reply->course;
        $replierName = $reply->user ? trim(($reply->user->name ?? '') . ' ' . ($reply->user->surname ?? '')) : 'The facilitator';
        if ($replierName === '') {
            $replierName = 'The facilitator';
        }
        $title = $course?->title ?? 'Course';
        $unitId = $reply->unit_id;
        $url = $course
            ? route('learn.show', [$course], $unitId ? ['unit' => $unitId] : [])
            : url('/');

        $message = "Your question in «{$title}» has a new reply from {$replierName}.";

        return [
            'variant' => 'qa_reply',
            'message' => $message,
            'action_url' => $url,
            'course_id' => $reply->course_id,
            'unit_id' => $unitId,
            'course_title' => $title,
        ];
    }
}
