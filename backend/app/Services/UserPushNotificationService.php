<?php

namespace App\Services;

use App\Models\User;

class UserPushNotificationService
{
    public function __construct(
        protected ExpoPushNotificationService $expoPush,
        protected WebPushNotificationService $webPush,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $this->expoPush->sendToUser($user, $title, $body, $data);
        $this->webPush->sendToUser($user, $title, $body, $data);
    }
}
