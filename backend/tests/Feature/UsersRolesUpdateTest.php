<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersRolesUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_update_writes_audit_log(): void
    {
        $systemAdminRole = Role::firstOrCreate(
            ['slug' => 'system_admin'],
            ['name' => 'System Admin']
        );
        $editorRole = Role::firstOrCreate(
            ['slug' => 'content_editor'],
            ['name' => 'Content Editor']
        );
        $studentRole = Role::firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student']
        );

        $admin = User::factory()->create(['surname' => 'Admin', 'email' => 'admin@example.com']);
        $admin->roles()->attach($systemAdminRole->id);

        $target = User::factory()->create(['surname' => 'Target', 'email' => 'target@example.com']);
        $target->roles()->attach($studentRole->id);

        $response = $this->actingAs($admin)->put(route('admin.users.update', ['user' => $target->id]), [
            'roles' => [$editorRole->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertTrue($target->fresh()->roles()->where('slug', 'content_editor')->exists());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.users.roles_updated',
            'target_type' => User::class,
            'target_id' => $target->id,
            'actor_user_id' => $admin->id,
        ]);

        $log = AuditLog::where('action', 'admin.users.roles_updated')
            ->where('target_id', $target->id)
            ->firstOrFail();

        $this->assertContains($editorRole->id, $log->metadata['after_role_ids'] ?? []);
    }
}
