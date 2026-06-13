<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSectionAccessTest extends TestCase
{
    use RefreshDatabase;

    private function role(string $slug, string $name): Role
    {
        return Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create(['surname' => 'Tester']);
        $user->roles()->attach($role->id);

        return $user;
    }

    public function test_dialogue_moderator_can_access_dialogue_admin(): void
    {
        $moderator = $this->userWithRole(
            $this->role('dialogue_moderator', 'Dialogue Moderator')
        );

        $response = $this->actingAs($moderator)->get(route('admin.dialogue.index'));

        $response->assertOk();
    }

    public function test_dialogue_moderator_cannot_access_constitution_admin(): void
    {
        $moderator = $this->userWithRole(
            $this->role('dialogue_moderator', 'Dialogue Moderator')
        );

        $response = $this->actingAs($moderator)->get(route('admin.constitution.index'));

        $response->assertForbidden();
    }

    public function test_student_cannot_access_admin_quick_search(): void
    {
        $student = $this->userWithRole($this->role('student', 'Student'));

        $response = $this->actingAs($student)->getJson(route('admin.quick-search', ['q' => 'test']));

        $response->assertForbidden();
    }

    public function test_system_admin_can_access_quick_search(): void
    {
        $admin = $this->userWithRole($this->role('system_admin', 'System Admin'));

        $response = $this->actingAs($admin)->getJson(route('admin.quick-search', ['q' => 'ab']));

        $response->assertOk()
            ->assertJsonPath('data.q', 'ab')
            ->assertJsonStructure(['data' => ['groups']]);
    }

    public function test_system_admin_can_submit_faq_question(): void
    {
        $admin = $this->userWithRole($this->role('system_admin', 'System Admin'));

        $response = $this->actingAs($admin)->post(route('admin.guide.faq.questions.store'), [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'subject' => 'Access',
            'message' => 'How do roles work?',
        ]);

        $response->assertRedirect(route('admin.guide.faq'));
    }
}
