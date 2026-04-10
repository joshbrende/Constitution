<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Lightweight outbound alerts for scheduled checks (Slack Incoming Webhook).
 * Uses POST JSON { "text": "..." }; Laravel's pingOnFailure() is GET-only and is not suitable.
 */
final class OpsAlerts
{
    public static function notifyPlainText(string $text): void
    {
        $url = (string) config('operations.alerts.slack_webhook', '');
        if ($url === '') {
            return;
        }

        try {
            $response = Http::timeout(8)
                ->asJson()
                ->post($url, ['text' => $text]);

            if (! $response->successful()) {
                Log::warning('ops_alert_slack_http_failed', [
                    'status' => $response->status(),
                    'body' => mb_substr($response->body(), 0, 500),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('ops_alert_slack_exception', ['message' => $e->getMessage()]);
        }
    }

    public static function queueHealthDegraded(): void
    {
        $name = (string) config('app.name', 'App');
        $url = (string) config('app.url', '');
        self::notifyPlainText(
            ":warning: *Queue / certificate health degraded*\n"
            . "App: {$name}\nURL: {$url}\nTime: " . now()->toIso8601String() . "\n"
            . 'Run `php artisan ops:queue-health` on the server for failed job and stale certificate counts.'
        );
    }
}
