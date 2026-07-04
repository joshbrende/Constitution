<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushNotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        if (! config('expo.push_enabled', true)) {
            return;
        }

        $tokens = $user->pushTokens()->pluck('expo_push_token')->filter()->unique()->values()->all();

        if ($tokens === []) {
            return;
        }

        $messages = array_map(fn (string $token) => array_filter([
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'sound' => 'default',
            'channelId' => 'academy',
        ]), $tokens);

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post((string) config('expo.push_api_url'), $messages);

            if (! $response->successful()) {
                Log::warning('expo.push.failed', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('expo.push.exception', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
