<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PortalNotifications
{
    public const TYPE_ACADEMY_PREFIX = 'academy.';

    public const TYPE_PORTAL_PREFIX = 'portal.';

    public const TYPE_ADMIN = 'portal.admin';

    public static function applyInboxFilter(MorphMany $query): MorphMany
    {
        return $query->where(function ($builder) {
            $builder->where('data->type', 'like', self::TYPE_ACADEMY_PREFIX.'%')
                ->orWhere('data->type', 'like', self::TYPE_PORTAL_PREFIX.'%');
        });
    }

    public static function unreadCount(User $user): int
    {
        return (int) $user->unreadNotifications()
            ->where(function ($builder) {
                $builder->where('data->type', 'like', self::TYPE_ACADEMY_PREFIX.'%')
                    ->orWhere('data->type', 'like', self::TYPE_PORTAL_PREFIX.'%');
            })
            ->count();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function mapForApi(User $user, int $limit = 30): array
    {
        return self::applyInboxFilter($user->notifications())
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($notification) => self::formatNotification($notification))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatNotification(object $notification): array
    {
        $data = $notification->data ?? [];

        return [
            'id' => $notification->id,
            'type' => $data['type'] ?? null,
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'receipt_number' => $data['receipt_number'] ?? null,
            'application_id' => $data['application_id'] ?? null,
            'campaign_id' => $data['campaign_id'] ?? null,
            'cta_type' => $data['cta_type'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'cta_tab' => $data['cta_tab'] ?? null,
            'cta_screen' => $data['cta_screen'] ?? null,
            'cta_params' => $data['cta_params'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'read' => $notification->read_at !== null,
            'at' => optional($notification->created_at)->toIso8601String(),
        ];
    }
}
