<?php

namespace Tests\Feature;

use App\Enums\CertificateApplicationStatus;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Permission;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Services\CertificateApplicationService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateApplicationAdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function seedRolesAndPermissions(): void
    {
        $this->seed(RoleSeeder::class);
        app(\App\Services\PermissionSyncService::class)->syncAll();
    }

    private function createApplicationForProvince(Province $province, string $status = 'payment_pending'): CertificateApplication
    {
        $user = User::factory()->create(['province_id' => $province->id]);
        $course = Course::create([
            'code' => 'MEM-ADM-'.$province->code,
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
            'receipt_number' => 'ZPF-REC-2026-'.strtoupper(substr($province->code, 0, 4)),
            'payment_reference_code' => strtoupper(substr($province->code, 0, 4)).'REF001',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => CertificateApplicationStatus::from($status === 'payment_pending' ? 'payment_pending' : $status),
            'exam_passed_at' => now(),
        ]);
    }

    private function assignRole(User $user, string $slug): void
    {
        $role = Role::where('slug', $slug)->firstOrFail();
        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    public function test_permissions_seeded_for_academy_workflow_actions(): void
    {
        $this->seedRolesAndPermissions();

        $manager = Role::where('slug', 'academy_manager')->firstOrFail();
        $provincial = Role::where('slug', 'provincial_admin')->firstOrFail();

        $this->assertTrue(
            Permission::where('slug', 'admin.action.presidium_publish')->exists()
        );
        $this->assertTrue(
            Permission::where('slug', 'admin.action.academy_payment_confirm')->exists(),
            'admin.action.academy_payment_confirm permission missing — run permissions:sync'
        );
        $this->assertTrue(
            $manager->permissions()->where('slug', 'admin.action.academy_payment_confirm')->exists()
        );
        $this->assertTrue(
            $provincial->permissions()->where('slug', 'admin.section.certificates')->exists()
        );
    }

    public function test_full_admin_workflow_from_payment_to_collection(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);

        $academyAdmin = User::factory()->create(['province_id' => $harare->id]);
        $this->assignRole($academyAdmin, 'academy_manager');

        $presidium = User::factory()->create();
        $this->assignRole($presidium, 'presidium');

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.confirm-payment', $application), [
                'payment_reference_note' => 'TELLER-123',
            ])
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::PresidiumPending, $application->status);
        $this->assertTrue($application->user->fresh()->hasRole('member'));

        $this->actingAs($presidium)
            ->post(route('admin.certificate-applications.presidium-approve', $application), [
                'presidium_note' => 'Approved',
            ])
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::PrintReady, $application->status);
        $this->assertNotNull($application->certificate_id);
        $this->assertDatabaseHas('certificates', ['id' => $application->certificate_id]);

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.mark-printed', $application))
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::Printed, $application->status);

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.ready-for-collection', $application), [
                'collection_office' => 'Harare HQ',
            ])
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::ReadyForCollection, $application->status);

        $this->actingAs($academyAdmin)
            ->post(route('admin.certificate-applications.mark-collected', $application))
            ->assertRedirect();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::Collected, $application->status);
    }

    public function test_provincial_admin_only_sees_own_province_applications(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $bulawayo = Province::where('code', 'bulawayo')->firstOrFail();

        $harareApp = $this->createApplicationForProvince($harare);
        $this->createApplicationForProvince($bulawayo);

        $provincialAdmin = User::factory()->create(['province_id' => $harare->id]);
        $this->assignRole($provincialAdmin, 'provincial_admin');

        $response = $this->actingAs($provincialAdmin)
            ->get(route('admin.certificate-applications.index', ['tab' => 'all']));

        $response->assertOk();
        $response->assertSee($harareApp->receipt_number);
        $response->assertDontSee('ZPF-REC-2026-BULA');
    }

    public function test_provincial_admin_cannot_presidium_approve(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);

        app(CertificateApplicationService::class)->confirmPayment(
            $application,
            User::factory()->create(),
            'REF'
        );

        $provincialAdmin = User::factory()->create(['province_id' => $harare->id]);
        $this->assignRole($provincialAdmin, 'provincial_admin');

        $this->actingAs($provincialAdmin)
            ->post(route('admin.certificate-applications.presidium-approve', $application))
            ->assertForbidden();
    }

    public function test_academy_admin_cannot_presidium_approve(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);
        app(CertificateApplicationService::class)->confirmPayment($application, User::factory()->create());

        $manager = User::factory()->create();
        $this->assignRole($manager, 'academy_manager');

        $this->actingAs($manager)
            ->post(route('admin.certificate-applications.presidium-approve', $application))
            ->assertForbidden();
    }

    public function test_presidium_pdf_download_requires_print_ready(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);

        $manager = User::factory()->create();
        $this->assignRole($manager, 'academy_manager');

        $this->actingAs($manager)
            ->get(route('admin.certificate-applications.certificate-pdf', $application))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_presidium_cannot_confirm_payment(): void
    {
        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);

        $presidium = User::factory()->create();
        $this->assignRole($presidium, 'presidium');

        $this->actingAs($presidium)
            ->post(route('admin.certificate-applications.confirm-payment', $application), [
                'payment_reference_note' => 'SHOULD-FAIL',
            ])
            ->assertForbidden();

        $application->refresh();
        $this->assertSame(CertificateApplicationStatus::PaymentPending, $application->status);
    }

    public function test_certificate_pdf_download_allowed_after_presidium_approval(): void
    {
        if (! class_exists(\TCPDF::class)) {
            $this->markTestSkipped('TCPDF not available.');
        }

        $this->seedRolesAndPermissions();

        $harare = Province::where('code', 'harare')->firstOrFail();
        $application = $this->createApplicationForProvince($harare);

        $academyAdmin = User::factory()->create();
        $presidium = User::factory()->create();
        $this->assignRole($academyAdmin, 'academy_manager');
        $this->assignRole($presidium, 'presidium');

        app(CertificateApplicationService::class)->confirmPayment($application, $academyAdmin, 'REF');
        $application->refresh();

        app(CertificateApplicationService::class)->presidiumApprove($application, $presidium, 'OK');
        $application->refresh();

        $this->actingAs($academyAdmin)
            ->get(route('admin.certificate-applications.certificate-pdf', $application))
            ->assertOk();
    }
}
