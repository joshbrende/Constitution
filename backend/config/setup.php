<?php

/**
 * First-run installation wizard security.
 *
 * Set SETUP_ACCESS_TOKEN in .env before opening /setup in production.
 * Pass the token as ?setup_token=... or header X-Setup-Token.
 */
return [

    'access_token' => env('SETUP_ACCESS_TOKEN', ''),

];
