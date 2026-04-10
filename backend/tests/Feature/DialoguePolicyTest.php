<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DialoguePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_thread_when_channel_requires_missing_role(): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Member', 'description' => 'Member']
        );

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-only',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $user = User::factory()->create(['surname' => 'Student']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'New topic',
        ]);

        $response->assertForbidden();
    }

    public function test_can_create_thread_when_user_has_required_role(): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'member'],
            ['name' => 'Member', 'description' => 'Member']
        );

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-only-2',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $user = User::factory()->create(['surname' => 'Member']);
        $user->roles()->attach($role->id);
        $user->load('roles');
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/dialogue/channels/{$channel->id}/threads", [
            'title' => 'New topic',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.id', fn ($id) => is_numeric($id));
    }
}
