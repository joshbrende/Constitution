<?php

return [

    /** Send academy push alerts via Expo Push API when users have registered tokens. */
    'push_enabled' => filter_var(env('EXPO_PUSH_ENABLED', true), FILTER_VALIDATE_BOOL),

    'push_api_url' => env('EXPO_PUSH_API_URL', 'https://exp.host/--/api/v2/push/send'),

];
