<?php

namespace App\Services;

use App\Models\MemberNotificationCampaign;
use App\Models\User;
use App\Notifications\Portal\AdminBroadcastNotification;
use Illuminate\Database\Eloquent\Builder;

class MemberNotificationDispatchService
{
    public function __construct(
        protected UserPushNotificationService $pushService,
    ) {}

    public function publish(MemberNotificationCampaign $campaign): int
    {
        if ($campaign->isPublished()) {
            return (int) $campaign->recipients_count;
        }

        $count = 0;

        $this->audienceQuery($campaign)
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($campaign, &$count) {
                foreach ($users as $user) {
                    $user->notify(new AdminBroadcastNotification($campaign));

                    $this->pushService->sendToUser(
                        $user,
                        $campaign->title,
                        mb_substr(strip_tags($campaign->body), 0, 160),
                        [
                            'type' => 'portal.admin',
                            'campaign_id' => $campaign->id,
                            'cta_tab' => $campaign->cta_tab,
                            'cta_screen' => $campaign->cta_screen,
                        ]
                    );

                    $count++;
                }
            });

        $campaign->update([
            'status' => MemberNotificationCampaign::STATUS_PUBLISHED,
            'published_at' => now(),
            'recipients_count' => $count,
        ]);

        return $count;
    }

    /**
     * @return Builder<User>
     */
    public function audienceQuery(MemberNotificationCampaign $campaign): Builder
    {
        $query = User::query();

        return match ($campaign->audience_type) {
            MemberNotificationCampaign::AUDIENCE_PROVINCE => $query->where('province_id', $campaign->province_id),
            MemberNotificationCampaign::AUDIENCE_ROLE => $query->whereHas(
                'roles',
                fn (Builder $roleQuery) => $roleQuery->where('roles.id', $campaign->role_id)
            ),
            default => $query,
        };
    }
}
