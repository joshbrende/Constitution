<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DialogueInboxService;
use App\Support\PortalNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Notifications
 *
 * In-app notification inbox for mobile (academy updates and admin broadcasts).
 */
class PortalNotificationController extends Controller
{
    public function __construct(
        protected DialogueInboxService $dialogueInbox,
    ) {}

    /**
     * List notifications
     *
     * Returns unread count and recent portal messages for the authenticated user.
     *
     * @response 200 {"data":{"unread_portal_messages_count":2,"portal_messages":[{"id":"…","title":"…","body":"…","read":false}]}}
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $portalUnread = PortalNotifications::unreadCount($user);
        $dialogueUnread = $this->dialogueInbox->unreadMessageCount($user);

        return response()->json([
            'data' => [
                'portal_messages' => PortalNotifications::mapForApi($user),
                'unread_portal_messages_count' => $portalUnread,
                'unread_dialogue_messages_count' => $dialogueUnread,
                'unread_count' => $portalUnread + $dialogueUnread,
            ],
        ]);
    }

    /**
     * Mark notification read
     */
    public function markRead(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $notification = PortalNotifications::applyInboxFilter($user->notifications())
            ->where('id', $notificationId)
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
                'unread_portal_messages_count' => PortalNotifications::unreadCount($user),
                'unread_dialogue_messages_count' => $this->dialogueInbox->unreadMessageCount($user),
                'unread_count' => PortalNotifications::unreadCount($user) + $this->dialogueInbox->unreadMessageCount($user),
            ],
        ]);
    }

    /**
     * Mark all notifications read
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $user->unreadNotifications()
            ->where(function ($builder) {
                $builder->where('data->type', 'like', PortalNotifications::TYPE_ACADEMY_PREFIX.'%')
                    ->orWhere('data->type', 'like', PortalNotifications::TYPE_PORTAL_PREFIX.'%');
            })
            ->update(['read_at' => now()]);

        $portalUnread = PortalNotifications::unreadCount($user);
        $dialogueUnread = $this->dialogueInbox->unreadMessageCount($user);

        return response()->json([
            'data' => [
                'unread_portal_messages_count' => $portalUnread,
                'unread_dialogue_messages_count' => $dialogueUnread,
                'unread_count' => $portalUnread + $dialogueUnread,
            ],
        ]);
    }
}
