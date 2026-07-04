<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPushToken;
use App\Notifications\Academy\ExamPassedPaymentRequiredNotification;
use App\Services\ExpoPushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobilePushAndPortalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_expo_push_token(): void
    {
        $user = User::factory()->create();
        $this->sanctumAs($user);

        $response = $this->postJson('/api/v1/profile/push-token', [
            'expo_push_token' => 'ExponentPushToken[test-token-abc]',
            'platform' => 'android',
            'device_name' => 'Pixel Test',
        ]);

        $response->assertOk()->assertJsonPath('data.registered', true);

        $this->assertDatabaseHas('user_push_tokens', [
            'user_id' => $user->id,
            'expo_push_token' => 'ExponentPushToken[test-token-abc]',
            'platform' => 'android',
        ]);
    }

    public function test_user_can_unregister_push_token(): void
    {
        $user = User::factory()->create();
        UserPushToken::create([
            'user_id' => $user->id,
            'expo_push_token' => 'ExponentPushToken[remove-me]',
            'platform' => 'ios',
        ]);

        $this->sanctumAs($user);

        $this->deleteJson('/api/v1/profile/push-token', [
            'expo_push_token' => 'ExponentPushToken[remove-me]',
        ])->assertNoContent();

        $this->assertDatabaseMissing('user_push_tokens', [
            'expo_push_token' => 'ExponentPushToken[remove-me]',
        ]);
    }

    public function test_mark_portal_notification_read_updates_unread_count(): void
    {
        $user = User::factory()->create();
        $course = \App\Models\Course::create([
            'code' => 'MEM-PUSH',
            'title' => 'Membership Course',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);
        $assessment = \App\Models\Assessment::create([
            'course_id' => $course->id,
            'title' => 'Final',
            'pass_mark' => 70,
            'status' => 'published',
        ]);
        $attempt = \App\Models\AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'question_ids' => [1],
            'score' => 100,
            'status' => 'graded',
            'started_at' => now(),
            'submitted_at' => now(),
        ]);
        $application = \App\Models\CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'assessment_attempt_id' => $attempt->id,
            'receipt_number' => 'ZPF-REC-PUSH',
            'payment_reference_code' => 'PUSHREF01',
            'fee_amount' => 25.00,
            'fee_currency' => 'USD',
            'status' => \App\Enums\CertificateApplicationStatus::PaymentPending,
            'exam_passed_at' => now(),
        ]);

        $user->notify(new ExamPassedPaymentRequiredNotification($application));

        $notification = $user->notifications()->first();
        $this->assertNotNull($notification);

        Sanctum::actingAs($user, ['academy:read']);

        $this->getJson('/api/v1/academy/summary')
            ->assertOk()
            ->assertJsonPath('data.unread_portal_messages_count', 1);

        $this->postJson("/api/v1/academy/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.unread_portal_messages_count', 0);
    }

    public function test_expo_push_service_posts_to_expo_api(): void
    {
        Http::fake([
            'exp.host/*' => Http::response(['data' => [['status' => 'ok']]], 200),
        ]);

        $user = User::factory()->create();
        UserPushToken::create([
            'user_id' => $user->id,
            'expo_push_token' => 'ExponentPushToken[push-test]',
            'platform' => 'android',
        ]);

        app(ExpoPushNotificationService::class)->sendToUser(
            $user,
            'Test title',
            'Test body',
            ['type' => 'academy.application.payment_pending', 'application_id' => 1]
        );

        Http::assertSent(function ($request) {
            $payload = $request->data();
            if (! is_array($payload) || ! isset($payload[0])) {
                return false;
            }

            return ($payload[0]['to'] ?? null) === 'ExponentPushToken[push-test]';
        });
    }
}
