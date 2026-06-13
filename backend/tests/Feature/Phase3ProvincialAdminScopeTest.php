<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3ProvincialAdminScopeTest extends TestCase
{
    use RefreshDatabase;

    private function harareProvince(): Province
    {
        return Province::query()->where('code', 'harare')->firstOrFail();
    }

    private function bulawayoProvince(): Province
    {
        return Province::query()->where('code', 'bulawayo')->firstOrFail();
    }

    private function provincialAdminRole(): Role
    {
        return Role::firstOrCreate(
            ['slug' => 'provincial_admin'],
            ['name' => 'Provincial Admin']
        );
    }

    private function userManagerRole(): Role
    {
        return Role::firstOrCreate(
            ['slug' => 'user_manager'],
            ['name' => 'User Manager']
        );
    }

    public function test_provincial_admin_user_index_is_scoped_to_own_province(): void
    {
        $harare = $this->harareProvince();
        $bulawayo = $this->bulawayoProvince();
        $role = $this->provincialAdminRole();

        $admin = User::factory()->create([
            'surname' => 'HarareAdmin',
            'email' => 'harare-admin@example.com',
            'province_id' => $harare->id,
        ]);
        $admin->roles()->attach($role->id);

        User::factory()->create([
            'name' => 'Visible',
            'surname' => 'HarareMember',
            'email' => 'visible-harare@example.com',
            'province_id' => $harare->id,
        ]);

        User::factory()->create([
            'name' => 'Hidden',
            'surname' => 'BulawayoMember',
            'email' => 'hidden-bulawayo@example.com',
            'province_id' => $bulawayo->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Visible HarareMember');
        $response->assertDontSee('Hidden BulawayoMember');
    }

    public function test_provincial_admin_cannot_edit_user_outside_province(): void
    {
        $harare = $this->harareProvince();
        $bulawayo = $this->bulawayoProvince();
        $role = $this->provincialAdminRole();

        $admin = User::factory()->create([
            'province_id' => $harare->id,
            'email' => 'scoped-admin@example.com',
        ]);
        $admin->roles()->attach($role->id);

        $other = User::factory()->create([
            'province_id' => $bulawayo->id,
            'email' => 'other-province@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', ['user' => $other->id]))
            ->assertNotFound();
    }

    public function test_user_manager_sees_users_from_all_provinces(): void
    {
        $harare = $this->harareProvince();
        $bulawayo = $this->bulawayoProvince();
        $role = $this->userManagerRole();

        $manager = User::factory()->create([
            'province_id' => $harare->id,
            'email' => 'manager@example.com',
        ]);
        $manager->roles()->attach($role->id);

        User::factory()->create([
            'name' => 'Harare',
            'surname' => 'Person',
            'province_id' => $harare->id,
        ]);

        User::factory()->create([
            'name' => 'Bulawayo',
            'surname' => 'Person',
            'province_id' => $bulawayo->id,
        ]);

        $response = $this->actingAs($manager)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Harare Person');
        $response->assertSee('Bulawayo Person');
    }

    public function test_quick_search_user_results_respect_province_scope(): void
    {
        $harare = $this->harareProvince();
        $bulawayo = $this->bulawayoProvince();
        $role = $this->provincialAdminRole();

        $admin = User::factory()->create([
            'province_id' => $harare->id,
            'email' => 'prov-search@example.com',
        ]);
        $admin->roles()->attach($role->id);

        User::factory()->create([
            'name' => 'Scoped',
            'surname' => 'Searchable',
            'email' => 'scoped-searchable@example.com',
            'province_id' => $harare->id,
        ]);

        User::factory()->create([
            'name' => 'Scoped',
            'surname' => 'HiddenElsewhere',
            'email' => 'scoped-hidden@example.com',
            'province_id' => $bulawayo->id,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.quick-search', ['q' => 'Scoped']));

        $response->assertOk();
        $labels = collect($response->json('data.groups'))
            ->firstWhere('key', 'users')['items'] ?? [];
        $labelText = collect($labels)->pluck('label')->implode(' ');

        $this->assertStringContainsString('Searchable', $labelText);
        $this->assertStringNotContainsString('HiddenElsewhere', $labelText);
    }

    public function test_members_index_is_scoped_for_provincial_admin(): void
    {
        $harare = $this->harareProvince();
        $bulawayo = $this->bulawayoProvince();
        $role = $this->provincialAdminRole();

        $course = Course::create([
            'code' => 'MEM-P3',
            'title' => 'Membership',
            'description' => 'Test',
            'level' => 'basic',
            'status' => 'published',
            'is_mandatory' => true,
            'grants_membership' => true,
        ]);

        $admin = User::factory()->create([
            'province_id' => $harare->id,
            'email' => 'members-admin@example.com',
        ]);
        $admin->roles()->attach($role->id);

        $localMember = User::factory()->create([
            'name' => 'Local',
            'surname' => 'CertHolder',
            'province_id' => $harare->id,
        ]);
        Certificate::create([
            'user_id' => $localMember->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZP-MEM-2026-P3LOCAL',
            'verification_code' => 'P3LOCAL1',
            'issued_at' => now(),
            'pdf_status' => 'ready',
        ]);

        $remoteMember = User::factory()->create([
            'name' => 'Remote',
            'surname' => 'CertHolder',
            'province_id' => $bulawayo->id,
        ]);
        Certificate::create([
            'user_id' => $remoteMember->id,
            'course_id' => $course->id,
            'certificate_number' => 'ZP-MEM-2026-P3REMOTE',
            'verification_code' => 'P3REMOT1',
            'issued_at' => now(),
            'pdf_status' => 'ready',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.members.index'));

        $response->assertOk();
        $response->assertSee('Local CertHolder');
        $response->assertDontSee('Remote CertHolder');
    }

    public function test_provincial_admin_cannot_access_backend_invite(): void
    {
        $role = $this->provincialAdminRole();
        $harare = $this->harareProvince();

        $admin = User::factory()->create([
            'province_id' => $harare->id,
            'email' => 'no-invite@example.com',
        ]);
        $admin->roles()->attach($role->id);

        $this->actingAs($admin)
            ->get(route('admin.users.invite.create'))
            ->assertForbidden();
    }

    public function test_provincial_admin_cannot_assign_admin_roles(): void
    {
        $harare = $this->harareProvince();
        $role = $this->provincialAdminRole();
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        Role::firstOrCreate(['slug' => 'content_editor'], ['name' => 'Content Editor']);

        $admin = User::factory()->create(['province_id' => $harare->id]);
        $admin->roles()->attach($role->id);

        $assignable = app(RoleAssignmentService::class)->assignableRoles($admin);
        $slugs = $assignable->pluck('slug')->all();

        $this->assertContains('student', $slugs);
        $this->assertNotContains('content_editor', $slugs);
        $this->assertNotContains('system_admin', $slugs);
    }
}
