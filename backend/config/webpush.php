<?php

return [

    /** Send Web Push (VAPID) alerts to registered PWA browsers. */
    'enabled' => filter_var(env('WEBPUSH_ENABLED', true), FILTER_VALIDATE_BOOL),

    /** mailto: or https: contact for VAPID subject. */
    'subject' => env('WEBPUSH_SUBJECT', 'mailto:support@zanupf.org.zw'),

    'public_key' => env('WEBPUSH_PUBLIC_KEY'),

    'private_key' => env('WEBPUSH_PRIVATE_KEY'),

];
