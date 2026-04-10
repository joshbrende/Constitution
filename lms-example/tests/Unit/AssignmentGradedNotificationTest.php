<?php

namespace Tests\Unit;

use App\Models\AssignmentSubmission;
use App\Notifications\AssignmentGradedNotification;
use Tests\TestCase;

class AssignmentGradedNotificationTest extends TestCase
{
    public function test_implements_should_queue(): void
    {
        $sub = new AssignmentSubmission;
        $n = new AssignmentGradedNotification($sub);
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $n);
    }

    public function test_via_returns_database_and_mail(): void
    {
        $sub = new AssignmentSubmission;
        $n = new AssignmentGradedNotification($sub);
        $notifiable = new \stdClass;
        $this->assertEquals(['database', 'mail'], $n->via($notifiable));
    }
}
