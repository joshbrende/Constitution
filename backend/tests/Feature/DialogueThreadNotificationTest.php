<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\MemberNotificationCampaign;
use App\Models\Role;
use App\Models\User;
use App\Services\MemberAutoNotificationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DialogueThreadNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_starting_chat_notifies_members_in_portal_inbox(): void
    {
        $moderatorRole = Role::query()->where('slug', 'dialogue_moderator')->firstOrFail();
        $memberRole = Role::query()->where('slug', 'member')->firstOrFail();

        $moderator = User::factory()->create();
        $moderator->roles()->attach($moderatorRole->id);

        $member = User::factory()->create();
        $member->roles()->attach($memberRole->id);

        $channel = DialogueChannel::create([
            'name' => 'Amendment Bill No. 3 (2026)',
            'slug' => 'amendment-3-2026',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $this->actingAs($moderator)
            ->post(route('admin.dialogue.threads.store', $channel), [
                'title' => 'District feedback',
                'opening_message' => 'Please share your views on implementation in your district.',
            ])
            ->assertRedirect();

        $campaign = MemberNotificationCampaign::query()
            ->where('trigger', MemberAutoNotificationService::TRIGGER_DIALOGUE_THREAD_STARTED)
            ->first();

        $this->assertNotNull($campaign);
        $this->assertTrue($campaign->isPublished());
        $this->assertSame('ChatTab', $campaign->cta_tab);
        $this->assertSame('ChatThread', $campaign->cta_screen);
        $this->assertSame($channel->id, $campaign->cta_params['channel']['id'] ?? null);

        $token = $member->createToken('test', ['profile:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/portal-notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_portal_messages_count', 1)
            ->assertJsonPath('data.unread_dialogue_messages_count', 1)
            ->assertJsonPath('data.unread_count', 2)
            ->assertJsonPath('data.portal_messages.0.title', 'New chat: District feedback')
            ->assertJsonPath('data.portal_messages.0.cta_screen', 'ChatThread');
    }
}
