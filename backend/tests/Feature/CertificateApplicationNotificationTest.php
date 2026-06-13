<?php

namespace Tests\Feature;

use App\Enums\CertificateApplicationStatus;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Academy\CertificateCollectedNotification;
use App\Notifications\Academy\CertificatePresidiumApprovedNotification;
use App\Notifications\Academy\CertificateReadyForCollectionNotification;
use App\Notifications\Academy\ExamPassedPaymentRequiredNotification;
use App\Notifications\Academy\PaymentConfirmedNotification;
use App\Services\CertificateApplicationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CertificateApplicationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createTestApplication(User $user, string $status = 'payment_pending'): CertificateApplication
    {
        $course = Course::create([
            'code' => 'MEM-NOTIF',
            'title' => 'Membership Course',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [1],
            'score' => 100,
            'status' => 'graded',
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        return CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => $attempt->id,
            'receipt_number' => 'ZP-REC-2026-NOTIF01',
            'payment_reference_code' => 'NOTIFREF01',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::from($status),
            'exam_passed_at' => now(),
        ]);
    }

    public function test_exam_pass_sends_payment_required_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $application = $this->createTestApplication($user);

        $service = app(CertificateApplicationService::class);
        $attempt = $application->assessmentAttempt;
        $attempt->load(['assessment.course', 'user']);

        // Simulate a fresh pass by deleting and recreating through service
        $application->delete();

        $service->createFromPassedAttempt($attempt);

        Notification::assertSentTo($user, ExamPassedPaymentRequiredNotification::class);
    }

    public function test_idempotent_application_does_not_resend_exam_pass_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $application = $this->createTestApplication($user);
        $attempt = $application->assessmentAttempt;
        $attempt->load(['assessment.course', 'user']);

        app(CertificateApplicationService::class)->createFromPassedAttempt($attempt);

        Notification::assertNothingSent();
    }

    public function test_notifications_sent_at_each_workflow_step(): void
    {
        Notification::fake();
        $this->seed(RoleSeeder::class);

        $harare = Province::where('code', 'harare')->firstOrFail();
        $student = User::factory()->create(['province_id' => $harare->id]);
        $academyAdmin = User::factory()->create();
        $presidium = User::factory()->create();

        $application = $this->createTestApplication($student, 'payment_pending');
        $service = app(CertificateApplicationService::class);

        $service->confirmPayment($application, $academyAdmin, 'TELLER-001');
        Notification::assertSentTo($student, PaymentConfirmedNotification::class);

        $application->refresh();
        $service->presidiumApprove($application, $presidium, 'Approved');
        Notification::assertSentTo($student, CertificatePresidiumApprovedNotification::class);

        $application->refresh();
        $service->markPrinted($application, $academyAdmin);
        Notification::assertNotSentTo($student, CertificateReadyForCollectionNotification::class);

        $application->refresh();
        $service->markReadyForCollection($application, $academyAdmin, 'Harare HQ');
        Notification::assertSentTo($student, CertificateReadyForCollectionNotification::class);

        $application->refresh();
        $service->markCollected($application, $academyAdmin);
        Notification::assertSentTo($student, CertificateCollectedNotification::class);
    }

    public function test_academy_summary_includes_portal_messages(): void
    {
        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $application = $this->createTestApplication($user);
        $application->delete();

        $attempt = AssessmentAttempt::where('user_id', $user->id)->first();
        $attempt->load(['assessment.course', 'user']);
        app(CertificateApplicationService::class)->createFromPassedAttempt($attempt);

        Sanctum::actingAs($user, ['academy:read']);

        $response = $this->getJson('/api/v1/academy/summary');
        $response->assertOk()
            ->assertJsonPath('data.pending_payment_applications', 1)
            ->assertJsonStructure([
                'data' => [
                    'portal_messages' => [
                        ['id', 'type', 'title', 'body', 'receipt_number', 'at'],
                    ],
                ],
            ]);

        $messages = $response->json('data.portal_messages');
        $this->assertNotEmpty($messages);
        $this->assertSame('academy.application.payment_pending', $messages[0]['type']);
        $this->assertSame('Exam passed – payment required', $messages[0]['title']);
    }
}
