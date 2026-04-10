<?php

/**
 * TTM Group branding for LMS (production: www.ttm-group.co.za).
 * Aligns with main site: index.html / ttm-group.co.za.
 */

return [
    'name' => env('BRAND_NAME', 'TTM Group'),
    'tagline' => env('BRAND_TAGLINE', 'AI Training Excellence'),
    'description' => env('BRAND_DESCRIPTION', 'TTM Group LMS – Premier AI training in South Africa. Access your courses, track progress, and earn certificates.'),
    'website_url' => env('BRAND_WEBSITE_URL', 'https://www.ttm-group.co.za'),
    'contact_email' => env('BRAND_CONTACT_EMAIL', 'events@ttm-group.co.za'),
    'contact_phone' => env('BRAND_CONTACT_PHONE', '+27 66 243 1698'),
    'address' => [
        'street' => '30 Dias Crescent',
        'locality' => 'Fourways',
        'region' => 'Johannesburg',
        'postal_code' => '2055',
        'country' => 'South Africa',
    ],
    'social' => [
        'facebook' => 'https://www.facebook.com/ttmgroup',
        'linkedin' => 'https://www.linkedin.com/company/ttm-group',
        'youtube' => 'https://www.youtube.com/ttmgroup',
        'twitter' => 'https://x.com/ttmgroup',
    ],
];
