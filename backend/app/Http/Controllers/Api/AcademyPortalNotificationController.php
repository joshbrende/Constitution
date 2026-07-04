<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Academy
 *
 * In-app portal notification read state (paired with `portal_messages` on academy summary).
 */
class AcademyPortalNotificationController extends Controller
{
    /**
     * Mark notification read
     *
     * Marks one academy portal notification as read and returns the updated unread count.
     *
     * @urlParam notificationId string required The Laravel notification UUID. Example: 9b7c8f2e-1a3b-4c5d-9e0f-123456789abc
     */
    public function markRead(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->where('data->type', 'like', 'academy.%')
            ->first();

        if (! $notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json([
            'data' => [
                'id' => $notification->id,
                'read' => true,
                'unread_portal_messages_count' => $this->unreadCount($user),
            ],
        ]);
    }

    /**
     * Mark all notifications read
     *
     * Marks every unread academy portal notification as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $user->unreadNotifications()
            ->where('data->type', 'like', 'academy.%')
            ->update(['read_at' => now()]);

        return response()->json([
            'data' => [
                'unread_portal_messages_count' => 0,
            ],
        ]);
    }

    private function unreadCount(\App\Models\User $user): int
    {
        return (int) $user->unreadNotifications()
            ->where('data->type', 'like', 'academy.%')
            ->count();
    }
}
