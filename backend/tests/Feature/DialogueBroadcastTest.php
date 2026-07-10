<?php

namespace Tests\Feature;

use App\Events\DialogueMessageChanged;
use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DialogueBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_message_dispatches_broadcast_event_when_broadcasting_enabled(): void
    {
        config(['broadcasting.default' => 'reverb']);

        Event::fake([DialogueMessageChanged::class]);

        $channel = DialogueChannel::create([
            'name' => 'Open',
            'slug' => 'open-broadcast',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => User::factory()->create()->id,
            'title' => 'Live chat',
            'status' => 'open',
        ]);

        DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'Hello everyone',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        Event::assertDispatched(DialogueMessageChanged::class);
    }

    public function test_soft_deleting_message_dispatches_broadcast_event(): void
    {
        config(['broadcasting.default' => 'reverb']);

        Event::fake([DialogueMessageChanged::class]);

        $message = DialogueMessage::create([
            'dialogue_thread_id' => DialogueThread::create([
                'dialogue_channel_id' => DialogueChannel::create([
                    'name' => 'Open',
                    'slug' => 'open-broadcast-delete',
                    'is_public' => true,
                    'min_role_slug' => null,
                ])->id,
                'created_by_user_id' => User::factory()->create()->id,
                'title' => 'Moderation',
                'status' => 'open',
            ])->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'Remove me',
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        Event::fake([DialogueMessageChanged::class]);

        $message->update(['is_deleted' => true]);

        Event::assertDispatched(DialogueMessageChanged::class);
    }
}
