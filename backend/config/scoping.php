<?php

/**
 * Admin geographic scoping (Phase 3).
 *
 * provincial_admin users are limited to users/members in their own province_id
 * unless they also hold a role listed in global_override_roles.
 */
return [

    'provincial_role' => 'provincial_admin',

    /** Roles that bypass province filters (national / global admin). */
    'global_override_roles' => [
        'system_admin',
        'user_manager',
        'presidium',
    ],

];
