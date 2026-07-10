<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueThread;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DialogueApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_dialogue_channels_requires_authentication(): void
    {
        $this->getJson('/api/v1/dialogue/channels')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_dialogue_channels(): void
    {
        DialogueChannel::create([
            'name' => 'National',
            'slug' => 'national',
            'description' => 'Test channel',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $user = User::factory()->create(['surname' => 'Chatter']);
        $this->sanctumAs($user);

        $response = $this->getJson('/api/v1/dialogue/channels');

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data');

        $this->assertSame('National', $response->json('data.0.name'));
    }

    public function test_member_can_post_message_in_open_thread(): void
    {
        $memberRole = Role::query()->where('slug', 'member')->firstOrFail();
        $channel = DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-chat',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $editor = User::factory()->create(['name' => 'System', 'surname' => '']);
        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editor->id,
            'title' => 'District feedback',
            'status' => 'open',
        ]);

        DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editor->id,
            'body' => 'Welcome — share your thoughts.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $member = User::factory()->create(['surname' => 'Citizen']);
        $member->roles()->attach($memberRole->id);
        $this->sanctumAs($member);

        $response = $this->postJson("/api/v1/dialogue/threads/{$thread->id}/messages", [
            'body' => 'Thank you for opening this chat.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'Thank you for opening this chat.')
            ->assertJsonPath('data.is_official', false);
    }

    public function test_messages_since_returns_new_and_moderated_updates(): void
    {
        $channel = DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-since',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $editor = User::factory()->create(['name' => 'System', 'surname' => '']);
        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editor->id,
            'title' => 'Moderation test',
            'status' => 'open',
        ]);

        $opening = DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editor->id,
            'body' => 'Opening message.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $member = User::factory()->create(['surname' => 'Poster']);
        $memberMsg = DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $member->id,
            'body' => 'Offensive content.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $since = $opening->fresh()->updated_at->toIso8601String();
        $this->travel(1)->seconds();
        $memberMsg->update(['is_deleted' => true]);

        $user = User::factory()->create(['surname' => 'Reader']);
        $this->sanctumAs($user);

        $response = $this->getJson("/api/v1/dialogue/threads/{$thread->id}/messages?since=".urlencode($since));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($memberMsg->id, $ids);

        $deleted = collect($response->json('data'))->firstWhere('id', $memberMsg->id);
        $this->assertTrue($deleted['is_deleted']);
        $this->assertNull($deleted['body']);
    }

    public function test_editor_messages_are_marked_official(): void
    {
        $channel = DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-official',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $editor = User::factory()->create(['name' => 'System', 'surname' => '']);
        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editor->id,
            'title' => 'Official reply',
            'status' => 'open',
        ]);

        DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editor->id,
            'body' => 'Editor guidance for everyone.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $user = User::factory()->create(['surname' => 'Reader']);
        $this->sanctumAs($user);

        $response = $this->getJson("/api/v1/dialogue/threads/{$thread->id}/messages");

        $response->assertOk();
        $this->assertTrue($response->json('data.0.is_official'));
    }
}
