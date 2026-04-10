<?php

namespace App\Mail;

use App\Models\SupportQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportQuestionSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportQuestion $question
    ) {}

    public function build(): self
    {
        $subject = trim((string) ($this->question->subject ?: 'FAQ Question'));

        return $this
            ->subject('[ZANU PF Admin] ' . $subject)
            ->replyTo($this->question->email, $this->question->name)
            ->view('emails.support-question-submitted');
    }
}

