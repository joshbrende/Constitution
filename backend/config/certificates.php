<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Certificate Expiry (Days)
    |--------------------------------------------------------------------------
    |
    | Number of days after issue when new certificates expire.
    | Set to 0 (or negative) to disable automatic expiry.
    |
    */
    'default_expiry_days' => (int) env('CERTIFICATE_DEFAULT_EXPIRY_DAYS', 730),

    /*
    |--------------------------------------------------------------------------
    | Certificate number prefix (ZANU PF)
    |--------------------------------------------------------------------------
    |
    | Issued certificates use: {prefix}-{YYYY}-{SUFFIX}
    | e.g. ZPF-MEM-2026-A1B2C3D4
    |
    */
    'certificate_number_prefix' => 'ZPF-MEM',
];

