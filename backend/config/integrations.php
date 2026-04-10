<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outbound integrations (government ICT)
    |--------------------------------------------------------------------------
    |
    | Backend is deployed on internal government servers. Outbound calls must be
    | deny-by-default and explicitly allowlisted by host.
    |
    */

    // Comma-separated list of allowed outbound hosts, e.g. "idm.gov.local,gateway.gov.local"
    'allowlist_hosts' => array_values(array_filter(array_map(
        fn ($h) => strtolower(trim($h)),
        explode(',', (string) env('INTEGRATION_ALLOWLIST_HOSTS', ''))
    ))),

    // Default HTTP settings for outbound calls
    'http' => [
        'timeout_seconds' => (int) env('INTEGRATION_HTTP_TIMEOUT_SECONDS', 10),
    ],
];

