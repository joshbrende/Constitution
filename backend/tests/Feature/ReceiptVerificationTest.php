<?php

namespace Tests\Feature;

use App\Enums\CertificateApplicationStatus;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeApplication(User $user, string $receipt, string $ref): CertificateApplication
    {
        $course = Course::create([
            'code' => 'MEM-VRF',
            'title' => 'Membership',
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
            'receipt_number' => $receipt,
            'payment_reference_code' => $ref,
            'fee_amount' => 25,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);
    }

    public function test_public_can_verify_receipt_by_number_and_reference(): void
    {
        $user = User::factory()->create();
        $application = $this->makeApplication($user, 'ZPF-REC-2026-HAR-000099', 'HAR-000099-K9');

        $this->get(route('receipt.verify', [
            'receipt' => $application->receipt_number,
            'ref' => $application->payment_reference_code,
        ]))
            ->assertOk()
            ->assertSee('Receipt found')
            ->assertSee($application->receipt_number)
            ->assertDontSee($user->email);
    }

    public function test_verify_by_public_id_path(): void
    {
        $user = User::factory()->create();
        $application = $this->makeApplication($user, 'ZPF-REC-2026-NAT-000001', 'NAT-000001-A1');

        $this->get('/verify-receipt/'.$application->public_id)
            ->assertOk()
            ->assertSee('Receipt found');
    }
}
