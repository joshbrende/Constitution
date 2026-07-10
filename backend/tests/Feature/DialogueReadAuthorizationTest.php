<?php

namespace Tests\Feature;

use App\Events\DialogueMessageChanged;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueThread;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DialogueReadAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_channel_role_cannot_list_threads(): void
    {
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-read-test',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => User::factory()->create()->id,
            'title' => 'Secret topic',
            'status' => 'open',
        ]);

        $student = User::factory()->create(['surname' => 'Student']);
        $this->sanctumAs($student);

        $this->getJson("/api/v1/dialogue/channels/{$channel->id}/threads")
            ->assertForbidden();
    }

    public function test_user_without_channel_role_cannot_read_messages(): void
    {
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-msg-test',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => User::factory()->create()->id,
            'title' => 'Topic',
            'status' => 'open',
        ]);

        $student = User::factory()->create(['surname' => 'Student']);
        $this->sanctumAs($student);

        $this->getJson("/api/v1/dialogue/threads/{$thread->id}/messages")
            ->assertForbidden();
    }

    public function test_user_without_channel_role_cannot_report_message(): void
    {
        Event::fake([DialogueMessageChanged::class]);

        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $channel = DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-report-test',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $editor = User::factory()->create();
        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $editor->id,
            'title' => 'Topic',
            'status' => 'open',
        ]);

        $message = DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $editor->id,
            'body' => 'Restricted message',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $student = User::factory()->create(['surname' => 'Student']);
        $this->sanctumAs($student);

        $this->postJson("/api/v1/dialogue/messages/{$message->id}/report", [
            'reason' => 'spam',
        ])->assertForbidden();
    }

    public function test_channel_list_excludes_inaccessible_channels(): void
    {
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        DialogueChannel::create([
            'name' => 'Open channel',
            'slug' => 'open-channel',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        DialogueChannel::create([
            'name' => 'Members only',
            'slug' => 'members-only-list',
            'is_public' => true,
            'min_role_slug' => 'member',
        ]);

        $student = User::factory()->create(['surname' => 'Student']);
        $this->sanctumAs($student);

        $response = $this->getJson('/api/v1/dialogue/channels');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Open channel', $names);
        $this->assertNotContains('Members only', $names);
    }
}
