<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\MemberNotificationCampaign;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MemberAutoNotificationService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MemberAutoNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        SiteSetting::set('installed_at', now()->toIso8601String());
    }

    public function test_publishing_course_dispatches_auto_notification_campaign(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('slug', 'system_admin')->first());

        $member = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.academy.courses.store'), [
                'code' => 'AUTO-101',
                'title' => 'Leadership Basics',
                'description' => 'Learn the fundamentals.',
                'level' => 'basic',
                'audience' => 'all',
                'status' => 'published',
            ])
            ->assertRedirect();

        $campaign = MemberNotificationCampaign::query()
            ->where('trigger', MemberAutoNotificationService::TRIGGER_COURSE_PUBLISHED)
            ->first();

        $this->assertNotNull($campaign);
        $this->assertTrue($campaign->isPublished());
        $this->assertSame(Course::class, $campaign->source_type);
        $this->assertStringContainsString('Leadership Basics', $campaign->title);

        Notification::assertSentTo($member, \App\Notifications\Portal\AdminBroadcastNotification::class);
    }

    public function test_course_publish_transition_notifies_members_in_portal_api(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('slug', 'system_admin')->first());

        $member = User::factory()->create();
        $member->roles()->attach(Role::where('slug', 'student')->first());

        $course = Course::create([
            'code' => 'DRAFT-1',
            'title' => 'Draft Course',
            'level' => 'basic',
            'audience' => 'all',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.academy.courses.update', $course), [
                'code' => 'DRAFT-1',
                'title' => 'Draft Course',
                'description' => 'Now live.',
                'level' => 'basic',
                'audience' => 'all',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.academy.index'));

        $token = $member->createToken('test', ['profile:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/portal-notifications')
            ->assertOk()
            ->assertJsonPath('data.unread_portal_messages_count', 1)
            ->assertJsonPath('data.portal_messages.0.title', 'Academy course now available: Draft Course');
    }
}
