<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWebPushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        if (! config('webpush.enabled', true)) {
            return;
        }

        $publicKey = (string) config('webpush.public_key');
        $privateKey = (string) config('webpush.private_key');

        if ($publicKey === '' || $privateKey === '') {
            return;
        }

        $subscriptions = $user->webPushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'url' => PushNotificationUrlResolver::resolve($data),
        ], JSON_THROW_ON_ERROR);

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => (string) config('webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ], [], 30, [], Log::getLogger());

        foreach ($subscriptions as $row) {
            $subscription = Subscription::create([
                'endpoint' => $row->endpoint,
                'publicKey' => $row->public_key,
                'authToken' => $row->auth_token,
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = (string) $report->getRequest()->getUri();

            if ($report->isSuccess()) {
                continue;
            }

            if ($report->isSubscriptionExpired()) {
                UserWebPushSubscription::query()->where('endpoint', $endpoint)->delete();
            }

            Log::warning('webpush.failed', [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'reason' => $report->getReason(),
            ]);
        }
    }
}
