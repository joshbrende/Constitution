<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueThread;
use App\Models\User;
use App\Support\DialogueEditor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DialogueInboxNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_notifications_include_unread_dialogue_messages(): void
    {
        $member = User::factory()->create(['surname' => 'Member']);
        $editor = User::factory()->create(['name' => 'System', 'surname' => '']);

        $channel = DialogueChannel::create([
            'name' => 'National dialogue',
            'slug' => 'national-inbox',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editor->id,
            'title' => 'Welcome thread',
            'status' => 'open',
        ]);

        DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editor->id,
            'body' => 'Editor has opened this conversation.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $token = $member->createToken('test', ['profile:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/portal-notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_dialogue_messages_count', 1)
            ->assertJsonPath('data.unread_count', 1);
    }

    public function test_reading_thread_clears_dialogue_unread_count(): void
    {
        $member = User::factory()->create(['surname' => 'Member']);
        $editorId = DialogueEditor::userId() ?? User::factory()->create(['name' => 'System'])->id;

        $channel = DialogueChannel::create([
            'name' => 'Open channel',
            'slug' => 'open-inbox',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editorId,
            'title' => 'Read test',
            'status' => 'open',
        ]);

        DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editorId,
            'body' => 'Please read this.',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $token = $member->createToken('test', ['profile:read', 'dialogue:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/v1/dialogue/threads/{$thread->id}/messages")
            ->assertOk();

        $this->withToken($token)
            ->getJson('/api/v1/portal-notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_dialogue_messages_count', 0)
            ->assertJsonPath('data.unread_count', 0);
    }
}
