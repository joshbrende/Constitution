<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API access & refresh token lifetimes
    |--------------------------------------------------------------------------
    |
    | Used by AuthController for Sanctum access tokens and stored refresh tokens.
    | Read via config() so values are available when config is cached.
    |
    */

    'access_token_expiry_minutes' => (int) env('ACCESS_TOKEN_EXPIRY_MINUTES', 15),

    'refresh_token_expiry_days' => (int) env('REFRESH_TOKEN_EXPIRY_DAYS', 7),

];
