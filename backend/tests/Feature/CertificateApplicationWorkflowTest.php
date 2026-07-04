<?php

namespace Tests\Feature;

use App\Enums\CertificateApplicationStatus;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Option;
use App\Models\Province;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CertificateApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_passing_exam_creates_application_with_receipt_and_defers_member_role(): void
    {
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $province = Province::query()->where('code', 'harare')->firstOrFail();
        $user = User::factory()->create([
            'surname' => 'Learner',
            'national_id' => '12-ABC123',
            'province_id' => $province->id,
        ]);
        $this->sanctumAs($user);

        $course = Course::create([
            'code' => 'MEM-FEE',
            'title' => 'Membership Course',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        Enrolment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q1',
            'order' => 1,
            'marks' => 1,
        ]);
        $correct = Option::create(['question_id' => $question->id, 'body' => 'Yes', 'is_correct' => true]);
        Option::create(['question_id' => $question->id, 'body' => 'No', 'is_correct' => false]);

        $assessmentResponse = $this->getJson("/api/v1/academy/assessments/{$assessment->id}");
        $assessmentResponse->assertOk();
        $token = $assessmentResponse->json('data.question_set_token');
        $this->assertNotEmpty($token);

        $startResponse = $this->postJson("/api/v1/academy/assessments/{$assessment->id}/attempts", [
            'question_set_token' => $token,
        ]);
        $startResponse->assertCreated();
        $attemptId = $startResponse->json('data.id');

        $submitResponse = $this->postJson("/api/v1/academy/attempts/{$attemptId}/submit", [
            'answers' => [
                ['question_id' => $question->id, 'option_id' => $correct->id],
            ],
        ]);
        $submitResponse->assertOk()->assertJsonPath('passed', true);

        $application = CertificateApplication::where('user_id', $user->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($application);
        $this->assertSame(CertificateApplicationStatus::PaymentPending, $application->status);
        $this->assertEquals(25.00, (float) $application->fee_amount);
        $this->assertSame('USD', $application->fee_currency);
        $this->assertNotEmpty($application->receipt_number);
        $this->assertNotEmpty($application->payment_reference_code);
        $this->assertMatchesRegularExpression('/^ZPF-REC-\d{4}-HAR-\d{6}$/', $application->receipt_number);

        $this->assertFalse($user->fresh()->hasRole('member'));
        $this->assertNull(Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first());
    }

    public function test_application_is_idempotent_for_same_user_and_course(): void
    {
        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $course = Course::create([
            'code' => 'MEM-IDEM',
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

        $service = app(\App\Services\CertificateApplicationService::class);
        $first = $service->createFromPassedAttempt($attempt);
        $second = $service->createFromPassedAttempt($attempt);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, CertificateApplication::count());
    }

    public function test_registration_succeeds_without_province(): void
    {
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane',
            'surname' => 'Doe',
            'email' => 'jane-province@example.org.zw',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'accept_terms' => true,
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'jane-province@example.org.zw')->firstOrFail();
        $this->assertNull($user->province_id);
    }

    public function test_student_can_list_and_view_own_application(): void
    {
        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $other = User::factory()->create(['national_id' => '12-XYZ999']);
        $course = Course::create([
            'code' => 'MEM-API',
            'title' => 'Membership',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => AssessmentAttempt::create([
                'assessment_id' => Assessment::create([
                    'course_id' => $course->id,
                    'title' => 'Final',
                    'pass_mark' => 70,
                    'status' => 'published',
                ])->id,
                'user_id' => $user->id,
                'question_ids' => [1],
                'score' => 100,
                'status' => 'graded',
                'started_at' => now(),
                'submitted_at' => now(),
            ])->id,
            'receipt_number' => 'ZPF-REC-2026-TEST0001',
            'payment_reference_code' => 'PAYREF1234',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);

        CertificateApplication::create([
            'user_id' => $other->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => AssessmentAttempt::create([
                'assessment_id' => Assessment::where('course_id', $course->id)->value('id'),
                'user_id' => $other->id,
                'question_ids' => [1],
                'score' => 100,
                'status' => 'graded',
                'started_at' => now(),
                'submitted_at' => now(),
            ])->id,
            'receipt_number' => 'ZPF-REC-2026-OTHER001',
            'payment_reference_code' => 'PAYREF5678',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $index = $this->getJson('/api/v1/academy/applications');
        $index->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.receipt_number', $application->receipt_number);

        $show = $this->getJson("/api/v1/academy/applications/{$application->id}");
        $show->assertOk()
            ->assertJsonPath('data.payment_reference_code', 'PAYREF1234')
            ->assertJsonStructure(['data' => ['portal_message', 'timeline', 'payment_offices', 'payment_instructions']]);
        $this->assertNotEmpty($show->json('data.portal_message'));

        $this->sanctumAs($other);
        $this->getJson("/api/v1/academy/applications/{$application->id}")->assertNotFound();
    }

    public function test_certificates_index_returns_empty_under_government_workflow(): void
    {
        config(['academy.student_certificate_download_enabled' => false]);

        $user = User::factory()->create();
        $this->sanctumAs($user);

        $response = $this->getJson('/api/v1/certificates');
        $response->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.certificates_disabled', true);
    }

    public function test_student_can_download_receipt_pdf(): void
    {
        if (! class_exists(\TCPDF::class)) {
            $this->markTestSkipped('TCPDF not available.');
        }

        $user = User::factory()->create(['national_id' => '12-ABC123']);
        $course = Course::create([
            'code' => 'MEM-PDF',
            'title' => 'Membership PDF',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => AssessmentAttempt::create([
                'assessment_id' => Assessment::create([
                    'course_id' => $course->id,
                    'title' => 'Final',
                    'pass_mark' => 70,
                    'status' => 'published',
                ])->id,
                'user_id' => $user->id,
                'question_ids' => [1],
                'score' => 100,
                'status' => 'graded',
                'started_at' => now(),
                'submitted_at' => now(),
            ])->id,
            'receipt_number' => 'ZPF-REC-2026-PDF0001',
            'payment_reference_code' => 'PDFREF1234',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);

        $this->sanctumAs($user);

        $response = $this->get("/api/v1/academy/applications/{$application->id}/receipt.pdf");
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_student_cannot_download_certificate_pdf_under_government_workflow(): void
    {
        config(['academy.student_certificate_download_enabled' => false]);

        $user = User::factory()->create();
        $course = Course::create([
            'code' => 'MEM-NODL',
            'title' => 'Membership',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZPF-MEM-2026-NODL001',
            'issued_at' => now(),
            'pdf_status' => 'ready',
            'pdf_path' => 'certificates/test.pdf',
        ]);

        $this->sanctumAs($user);

        $this->getJson("/api/v1/certificates/{$certificate->id}/pdf")->assertForbidden();
        $this->postJson("/api/v1/certificates/{$certificate->id}/generate")->assertForbidden();
    }

    public function test_full_workflow_from_pass_to_collection(): void
    {
        if (! class_exists(\TCPDF::class)) {
            $this->markTestSkipped('TCPDF not available.');
        }

        $this->seed(\Database\Seeders\RoleSeeder::class);
        app(\App\Services\PermissionSyncService::class)->syncAll();

        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $harare = Province::query()->where('code', 'harare')->firstOrFail();
        $student = User::factory()->create([
            'surname' => 'Graduate',
            'national_id' => '12-FULL001',
            'province_id' => $harare->id,
        ]);
        $this->sanctumAs($student);

        $course = Course::create([
            'code' => 'MEM-FULL',
            'title' => 'Membership Course',
            'description' => 'Full workflow test',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);

        Enrolment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'body' => 'Q1',
            'order' => 1,
            'marks' => 1,
        ]);
        $correct = Option::create(['question_id' => $question->id, 'body' => 'Yes', 'is_correct' => true]);
        Option::create(['question_id' => $question->id, 'body' => 'No', 'is_correct' => false]);

        $token = $this->getJson("/api/v1/academy/assessments/{$assessment->id}")
            ->assertOk()
            ->json('data.question_set_token');

        $attemptId = $this->postJson("/api/v1/academy/assessments/{$assessment->id}/attempts", [
            'question_set_token' => $token,
        ])->assertCreated()->json('data.id');

        $this->postJson("/api/v1/academy/attempts/{$attemptId}/submit", [
            'answers' => [['question_id' => $question->id, 'option_id' => $correct->id]],
        ])->assertOk()->assertJsonPath('passed', true);

        $application = CertificateApplication::where('user_id', $student->id)->first();
        $this->assertNotNull($application);
        $this->assertSame(CertificateApplicationStatus::PaymentPending, $application->status);

        $this->getJson('/api/v1/academy/summary')
            ->assertOk()
            ->assertJsonPath('data.pending_payment_applications', 1);

        $academyAdmin = User::factory()->create(['province_id' => $harare->id]);
        $academyAdmin->roles()->attach(Role::where('slug', 'academy_manager')->firstOrFail()->id);

        $presidium = User::factory()->create();
        $presidium->roles()->attach(Role::where('slug', 'presidium')->firstOrFail()->id);

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.confirm-payment', $application), [
                'payment_reference_note' => 'TELLER-FULL',
            ])
            ->assertRedirect();

        $application->refresh();
        $this->assertTrue($student->fresh()->hasRole('member'));

        $this->actingAs($presidium)
            ->post(route('admin.certificate-applications.presidium-approve', $application), [
                'presidium_note' => 'Approved for printing',
            ])
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::PrintReady, $application->status);
        $this->assertNotNull($application->certificate_id);

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.mark-printed', $application))
            ->assertRedirect();

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.ready-for-collection', $application), [
                'collection_office' => 'Harare HQ',
            ])
            ->assertRedirect();

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.mark-collected', $application))
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::Collected, $application->status);

        $this->assertDatabaseHas('audit_logs', ['action' => 'academy.application.created']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'academy.application.collected']);
    }
}
