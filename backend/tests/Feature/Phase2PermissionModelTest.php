<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use App\Services\TokenAbilityService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Phase2PermissionModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_dialogue_moderator_has_dialogue_section_permission_only_among_sections(): void
    {
        $role = Role::query()->where('slug', 'dialogue_moderator')->firstOrFail();
        $slugs = $role->permissions()->pluck('slug')->all();

        $this->assertContains('admin.section.dialogue', $slugs);
        $this->assertNotContains('admin.section.constitution', $slugs);
    }

    public function test_system_admin_has_presidium_publish_action_permission(): void
    {
        $role = Role::query()->where('slug', 'system_admin')->firstOrFail();
        $slugs = $role->permissions()->pluck('slug')->all();

        $this->assertContains('admin.action.presidium_publish', $slugs);
        $this->assertContains('admin.action.platform_settings', $slugs);
    }

    public function test_student_role_has_api_token_abilities_in_database(): void
    {
        $role = Role::query()->where('slug', 'student')->firstOrFail();
        $apiSlugs = $role->permissions()->where('domain', Permission::DOMAIN_API)->pluck('slug')->all();

        $this->assertContains('profile:read', $apiSlugs);
        $this->assertContains('academy:write', $apiSlugs);
        $this->assertContains('dialogue:read', $apiSlugs);
        $this->assertNotContains('dialogue:write', $apiSlugs);
    }

    public function test_admin_access_service_uses_permissions_when_seeded(): void
    {
        $service = app(\App\Services\AdminAccessService::class);
        $this->assertTrue($service->permissionsEnabled());

        $moderator = User::factory()->create(['surname' => 'Mod']);
        $moderator->roles()->attach(Role::where('slug', 'dialogue_moderator')->firstOrFail()->id);

        $this->assertTrue($service->canAccessSection($moderator, 'dialogue'));
        $this->assertFalse($service->canAccessSection($moderator, 'constitution'));
    }
}
