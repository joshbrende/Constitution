<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use App\Services\TokenAbilityService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Phase2TokenAbilitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_login_issues_scoped_token_not_wildcard(): void
    {
        $studentRole = Role::query()->where('slug', 'student')->firstOrFail();
        $user = User::factory()->create([
            'surname' => 'Student',
            'email' => 'scoped@example.com',
            'password' => 'Password123!',
        ]);
        $user->roles()->attach($studentRole->id);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'scoped@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertOk();

        $user->refresh();
        $token = $user->tokens()->firstOrFail();
        $abilities = $token->abilities ?? [];

        $this->assertNotContains('*', $abilities);
        $this->assertContains('profile:read', $abilities);
        $this->assertContains('dialogue:write', $abilities);
    }

    public function test_token_without_dialogue_write_cannot_post_thread(): void
    {
        DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-ability-test',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $user = User::factory()->create(['surname' => 'Limited']);
        Sanctum::actingAs($user, ['profile:read', 'dialogue:read']);

        $channel = DialogueChannel::firstOrFail();

        $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'Should fail',
        ])->assertForbidden();
    }

    public function test_token_with_dialogue_write_can_post_thread(): void
    {
        $channel = DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-ability-write',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $user = User::factory()->create(['surname' => 'Writer']);
        Sanctum::actingAs($user, ['dialogue:read', 'dialogue:write']);

        $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'Allowed topic',
        ])->assertCreated();
    }

    public function test_token_ability_service_matches_student_role(): void
    {
        $studentRole = Role::query()->where('slug', 'student')->firstOrFail();
        $user = User::factory()->create(['surname' => 'Student']);
        $user->roles()->attach($studentRole->id);
        $user->load('roles.permissions');

        $abilities = app(TokenAbilityService::class)->abilitiesForUser($user);

        $this->assertContains('certificates:read', $abilities);
        $this->assertContains('comments:write', $abilities);
    }
}
