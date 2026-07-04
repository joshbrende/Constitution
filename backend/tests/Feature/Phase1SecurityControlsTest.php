<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Phase1SecurityControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_api_route_binding_returns_not_found_for_other_users(): void
    {
        $owner = User::factory()->create(['surname' => 'Owner']);
        $other = User::factory()->create(['surname' => 'Other']);

        $course = Course::create([
            'code' => 'MEM-401',
            'title' => 'Membership',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => true,
            'grants_membership' => true,
        ]);

        $cert = \App\Models\Certificate::create([
            'user_id' => $owner->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZPF-MEM-2026-SCOPED01',
            'verification_code' => 'SCOPED01',
            'issued_at' => now(),
            'pdf_status' => 'pending',
        ]);

        $this->sanctumAs($other);

        $this->postJson("/api/v1/certificates/{$cert->id}/generate")->assertNotFound();
    }

    public function test_assessment_attempt_submit_returns_not_found_for_other_users(): void
    {
        $owner = User::factory()->create(['surname' => 'Owner']);
        $other = User::factory()->create(['surname' => 'Other']);

        $course = Course::create([
            'code' => 'MEM-402',
            'title' => 'Membership',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => true,
            'grants_membership' => true,
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'status' => 'published',
            'pass_mark' => 70,
            'time_limit_minutes' => 45,
            'questions_per_attempt' => 5,
        ]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $owner->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'question_ids' => [],
        ]);

        $this->sanctumAs($other);

        $this->postJson("/api/v1/academy/attempts/{$attempt->id}/submit", [
            'answers' => [],
        ])->assertNotFound();
    }

    public function test_platform_settings_update_writes_audit_log(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'system_admin'], ['name' => 'System Admin']);
        $admin = User::factory()->create(['surname' => 'Admin', 'email' => 'admin@example.org.zw']);
        $admin->roles()->attach($adminRole->id);

        SiteSetting::set('org_name', 'Before Org');

        $response = $this->actingAs($admin)->put(route('admin.platform-settings.update'), [
            'org_name' => 'After Org',
            'support_email' => 'ops@example.org.zw',
            'public_site_url' => '',
            'legal_privacy_url' => 'https://www.zanupf.org.zw/privacy',
            'legal_terms_url' => 'https://www.zanupf.org.zw/terms',
            'legal_cookies_url' => 'https://www.zanupf.org.zw/cookies',
            'enable_dialogue' => '1',
            'require_national_id' => '1',
        ]);

        $response->assertRedirect(route('admin.platform-settings.edit'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.platform_settings.updated',
            'actor_user_id' => $admin->id,
        ]);
    }

    public function test_audit_log_index_writes_meta_audit_entry(): void
    {
        $viewerRole = Role::firstOrCreate(['slug' => 'audit_viewer'], ['name' => 'Audit Viewer']);
        $viewer = User::factory()->create(['surname' => 'Viewer', 'email' => 'viewer@example.org.zw']);
        $viewer->roles()->attach($viewerRole->id);

        $response = $this->actingAs($viewer)->get(route('admin.audit-logs.index', ['action' => 'auth']));

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'audit_logs.viewed',
            'actor_user_id' => $viewer->id,
        ]);
    }

    public function test_user_edit_writes_pii_view_audit_log(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'system_admin'], ['name' => 'System Admin']);
        $admin = User::factory()->create(['surname' => 'Admin', 'email' => 'admin@example.org.zw']);
        $admin->roles()->attach($adminRole->id);

        $target = User::factory()->create(['surname' => 'Target', 'email' => 'target@example.org.zw']);

        $response = $this->actingAs($admin)->get(route('admin.users.edit', ['user' => $target->id]));

        $response->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.users.pii_viewed',
            'target_type' => User::class,
            'target_id' => $target->id,
            'actor_user_id' => $admin->id,
        ]);
    }
}
