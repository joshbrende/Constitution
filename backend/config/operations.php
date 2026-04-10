<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outbound alerts (Slack Incoming Webhook)
    |--------------------------------------------------------------------------
    |
    | OPS_ALERT_SLACK_WEBHOOK_URL — used when scheduled ops:queue-health fails.
    | Falls back to LOG_SLACK_WEBHOOK_URL if unset (same channel as log alerts).
    |
    | Optional error tracking: composer require sentry/sentry-laravel, then set
    | SENTRY_LARAVEL_DSN in .env (see https://docs.sentry.io/platforms/php/guides/laravel/).
    |
    */
    'alerts' => [
        'slack_webhook' => env('OPS_ALERT_SLACK_WEBHOOK_URL') ?: env('LOG_SLACK_WEBHOOK_URL'),
    ],

    'cleanup' => [
        // Delete audit log rows older than this many days.
        'audit_log_retention_days' => (int) env('AUDIT_LOG_RETENTION_DAYS', 365),
        // Delete refresh tokens that are expired or revoked and older than this many days.
        'refresh_token_retention_days' => (int) env('REFRESH_TOKEN_RETENTION_DAYS', 30),
    ],
    'queue_health' => [
        // Warn when failed jobs count exceeds this value.
        'max_failed_jobs' => (int) env('QUEUE_HEALTH_MAX_FAILED_JOBS', 10),
        // Certificates stuck in pending/generating longer than this many minutes are considered stale.
        'stale_certificate_minutes' => (int) env('QUEUE_HEALTH_STALE_CERTIFICATE_MINUTES', 30),
    ],
];

