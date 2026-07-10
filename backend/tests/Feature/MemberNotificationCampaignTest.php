<?php

namespace Tests\Feature;

use App\Models\MemberNotificationCampaign;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MemberNotificationCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_publish_notification_to_all_members(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('slug', 'system_admin')->first());

        $memberA = User::factory()->create();
        $memberB = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.member-notifications.store'), [
                'title' => 'Congress reminder',
                'body' => 'Annual congress opens next week.',
                'audience_type' => 'all',
                'cta_type' => 'none',
                'publish_now' => '1',
            ])
            ->assertRedirect(route('admin.member-notifications.index'));

        $campaign = MemberNotificationCampaign::first();
        $this->assertNotNull($campaign);
        $this->assertTrue($campaign->isPublished());
        $this->assertSame(3, $campaign->recipients_count);

        Notification::assertSentTo($memberA, \App\Notifications\Portal\AdminBroadcastNotification::class);
        Notification::assertSentTo($memberB, \App\Notifications\Portal\AdminBroadcastNotification::class);
    }

    public function test_portal_notifications_api_lists_admin_broadcast(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('slug', 'student')->first());

        $campaign = MemberNotificationCampaign::create([
            'title' => 'Branch meeting',
            'body' => 'All cells meet Friday.',
            'audience_type' => 'all',
            'cta_type' => 'none',
            'status' => MemberNotificationCampaign::STATUS_PUBLISHED,
            'published_at' => now(),
            'recipients_count' => 1,
        ]);

        $user->notify(new \App\Notifications\Portal\AdminBroadcastNotification($campaign));

        $token = $user->createToken('test', ['profile:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/portal-notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_portal_messages_count', 1)
            ->assertJsonPath('data.portal_messages.0.title', 'Branch meeting')
            ->assertJsonPath('data.portal_messages.0.type', 'portal.admin');
    }
}
