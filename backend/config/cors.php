<?php

/**
 * CORS is enforced by `\Illuminate\Http\Middleware\HandleCors`.
 *
 * Production requirement:
 * - Only allow requests from approved production domain(s).
 *
 * Configure via env:
 * - CORS_ALLOWED_ORIGINS="https://app.example.com,https://www.app.example.com"
 *
 * Note:
 * We intentionally avoid Laravel helpers here and read from $_ENV/$_SERVER
 * so this file stays compatible with the bootstrap sequence.
 */

$raw = $_ENV['CORS_ALLOWED_ORIGINS'] ?? $_SERVER['CORS_ALLOWED_ORIGINS'] ?? '';
$origins = array_values(array_filter(array_map(static function ($v) {
    $v = trim((string) $v);
    return $v !== '' ? $v : null;
}, explode(',', $raw))));

// If not configured:
// - Production: deny all cross-origin by default (must be set on client installs)
// - Non-production: allow all for local development convenience
$appEnv = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'production';
if (empty($origins)) {
    $origins = $appEnv === 'production' ? [] : ['*'];
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Mobile uses Bearer tokens; cookies are not required for the API.
    'supports_credentials' => false,
];

