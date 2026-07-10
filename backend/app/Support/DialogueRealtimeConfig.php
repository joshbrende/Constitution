<?php

namespace App\Support;

class DialogueRealtimeConfig
{
    /**
     * @return array<string, mixed>
     */
    public static function clientPayload(): array
    {
        $enabled = config('broadcasting.default') === 'reverb';
        $key = (string) config('broadcasting.connections.reverb.key', '');

        if (! $enabled || $key === '') {
            return [
                'enabled' => false,
            ];
        }

        return [
            'enabled' => true,
            'key' => $key,
            'host' => (string) env('REVERB_CLIENT_HOST', env('REVERB_HOST', 'localhost')),
            'port' => (int) env('REVERB_PORT', 8080),
            'scheme' => (string) env('REVERB_SCHEME', 'http'),
            'auth_endpoint' => url('/api/v1/broadcasting/auth'),
        ];
    }
}
