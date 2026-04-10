<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_index_renders_for_authenticated_user(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Test notification',
                'action_url' => route('courses.index'),
            ],
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
    }

    public function test_read_and_go_marks_notification_as_read_and_redirects_to_action_url(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Go to courses',
                'action_url' => route('courses.index'),
            ],
        ]);

        $response = $this->actingAs($user)->get(route('notifications.read-and-go', $notification->id));

        $response->assertRedirect(route('courses.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_read_marks_all_unread_notifications_as_read(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user2@example.com',
            'password' => 'password',
        ]);

        $n1 = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'First'],
        ]);

        $n2 = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Second'],
        ]);

        $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $response->assertRedirect(route('notifications.index'));
        $this->assertNotNull($n1->fresh()->read_at);
        $this->assertNotNull($n2->fresh()->read_at);
    }
}

