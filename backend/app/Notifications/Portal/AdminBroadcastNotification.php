<?php

namespace App\Notifications\Portal;

use App\Models\MemberNotificationCampaign;
use Illuminate\Notifications\Notification;

class AdminBroadcastNotification extends Notification
{
    public function __construct(
        public MemberNotificationCampaign $campaign,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'portal.admin',
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'body' => $this->campaign->body,
            'cta_type' => $this->campaign->cta_type,
            'cta_label' => $this->campaign->cta_label,
            'cta_tab' => $this->campaign->cta_tab,
            'cta_screen' => $this->campaign->cta_screen,
            'cta_params' => $this->campaign->cta_params,
            'cta_url' => $this->campaign->cta_url,
        ];
    }
}
