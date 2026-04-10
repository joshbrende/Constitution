<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AssignmentSubmission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->submission->load(['course', 'assignment.unit']);
        $course = $this->submission->course;
        $unit = $this->submission->assignment?->unit;
        $url = $course
            ? route('learn.show', [$course], $unit ? ['unit' => $unit->id] : [])
            : url('/');
        $score = $this->submission->score ?? 0;
        $max = $this->submission->max_points ?: 100;
        $title = $course?->title ?? 'Assignment';

        return (new MailMessage)
            ->subject('Your assignment has been graded')
            ->line("Your assignment in «{$title}» has been graded: {$score}/{$max}.")
            ->action('View in course', $url)
            ->line('Thank you for using ' . config('app.name') . '.');
    }

    public function toArray(object $notifiable): array
    {
        $submission = $this->submission;
        $submission->load(['course', 'assignment.unit']);
        $course = $submission->course;
        $unit = $submission->assignment?->unit;
        $url = $course
            ? route('learn.show', [$course], $unit ? ['unit' => $unit->id] : [])
            : url('/');

        $score = $submission->score ?? 0;
        $max = $submission->max_points ?: 100;
        $title = $course?->title ?? 'Assignment';
        $message = "Your assignment in «{$title}» has been graded: {$score}/{$max}.";

        return [
            'variant' => 'assignment_graded',
            'message' => $message,
            'action_url' => $url,
            'course_id' => $submission->course_id,
            'unit_id' => $unit?->id,
            'course_title' => $title,
            'score' => $score,
            'max_points' => $max,
        ];
    }
}
