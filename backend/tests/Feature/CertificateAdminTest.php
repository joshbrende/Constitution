<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateAdminTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        $user = User::factory()->create([
            'surname' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        $role = Role::create([
            'name' => 'System Admin',
            'slug' => 'system_admin',
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    private function certificateFixture(): Certificate
    {
        $recipient = User::factory()->create(['surname' => 'Member']);
        $course = Course::create([
            'code' => 'MEM-101',
            'title' => 'Membership',
            'description' => 'Membership course',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => true,
            'grants_membership' => true,
        ]);

        return Certificate::create([
            'user_id' => $recipient->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZP-MEM-2026-ABC12345',
            'verification_code' => 'VERIFY99',
            'issued_at' => now(),
            'pdf_status' => 'pending',
        ]);
    }

    public function test_certificate_search_mode_filter_works(): void
    {
        $admin = $this->adminUser();
        $this->certificateFixture();

        $response = $this->actingAs($admin)->get('/admin/certificates?search_mode=certificate_number&q=ABC12345');
        $response->assertOk();
        $response->assertSee('ZP-MEM-2026-ABC12345');
    }

    public function test_revoke_and_reinstate_write_audit_logs_and_metadata(): void
    {
        $admin = $this->adminUser();
        $certificate = $this->certificateFixture();

        $revoke = $this->actingAs($admin)->post("/admin/certificates/{$certificate->id}/revoke", [
            'reason' => 'Fraud report',
        ]);
        $revoke->assertRedirect('/admin/certificates');

        $certificate->refresh();
        $this->assertNotNull($certificate->revoked_at);
        $this->assertSame('Fraud report', $certificate->revoked_reason);
        $this->assertSame($admin->id, $certificate->revoked_by_user_id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'certificate.revoked',
            'target_type' => Certificate::class,
            'target_id' => $certificate->id,
            'actor_user_id' => $admin->id,
        ]);

        $reinstate = $this->actingAs($admin)->post("/admin/certificates/{$certificate->id}/unrevoke");
        $reinstate->assertRedirect('/admin/certificates');

        $certificate->refresh();
        $this->assertNull($certificate->revoked_at);
        $this->assertNull($certificate->revoked_reason);
        $this->assertNull($certificate->revoked_by_user_id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'certificate.reinstated',
            'target_type' => Certificate::class,
            'target_id' => $certificate->id,
            'actor_user_id' => $admin->id,
        ]);

        $this->assertGreaterThanOrEqual(2, AuditLog::count());
    }
}

