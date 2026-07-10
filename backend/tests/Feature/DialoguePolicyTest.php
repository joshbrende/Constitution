<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DialoguePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_cannot_create_thread_when_channel_requires_missing_role(): void
    {
        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-only',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $user = User::factory()->create(['surname' => 'Student']);
        $this->sanctumAs($user);

        $response = $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'New topic',
        ]);

        $response->assertForbidden();
    }

    public function test_member_with_channel_access_cannot_create_thread(): void
    {
        $role = Role::query()->where('slug', 'member')->firstOrFail();

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-only-2',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $user = User::factory()->create(['surname' => 'Member']);
        $user->roles()->attach($role->id);
        $user->load('roles');
        $this->sanctumAs($user);

        $response = $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'New topic',
        ]);

        $response->assertForbidden();
    }

    public function test_dialogue_moderator_can_create_thread(): void
    {
        $role = Role::query()->where('slug', 'dialogue_moderator')->firstOrFail();

        $channel = DialogueChannel::create([
            'name' => 'National',
            'slug' => 'national-mod-test',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $user = User::factory()->create(['surname' => 'Moderator']);
        $user->roles()->attach($role->id);
        $user->load('roles');
        $this->sanctumAs($user, ['dialogue:read']);

        $response = $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'Editor topic',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.id', fn ($id) => is_numeric($id));
    }
}
