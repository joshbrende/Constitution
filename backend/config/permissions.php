<?php

/**
 * Permission definitions for admin (DB-backed) and API (Sanctum token abilities).
 *
 * Admin section → role mapping is synced from config/admin.php by PermissionSyncService.
 * API abilities are granted to roles via role_api_abilities below.
 */
return [

    'admin_actions' => [
        'presidium_publish' => [
            'name' => 'Publish / approve constitutional amendments',
            'roles' => ['presidium', 'system_admin'],
        ],
        'platform_settings' => [
            'name' => 'Manage platform settings',
            'roles' => ['system_admin'],
        ],
        'roles_manage' => [
            'name' => 'Manage role definitions (CRUD)',
            'roles' => ['system_admin'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanctum token abilities (mobile API)
    |--------------------------------------------------------------------------
    | Ability strings are attached to access tokens and enforced on route groups.
    */
    'api_ability_labels' => [
        'profile:read' => 'Read own profile and provinces',
        'profile:write' => 'Update or delete own profile',
        'academy:read' => 'Browse academy courses, assessments, badges',
        'academy:write' => 'Enrol, start attempts, submit assessments',
        'certificates:read' => 'List and download own certificates',
        'certificates:write' => 'Request certificate PDF generation',
        'dialogue:read' => 'Read dialogue channels, threads, messages',
        'dialogue:write' => 'Post threads/messages, report, block users',
        'projects:read' => 'View priority projects',
        'projects:write' => 'Like priority projects',
        'comments:write' => 'Post constitution section comments',
    ],

    'role_api_abilities' => [
        'student' => [
            'profile:read',
            'profile:write',
            'academy:read',
            'academy:write',
            'certificates:read',
            'certificates:write',
            'dialogue:read',
            'dialogue:write',
            'projects:read',
            'projects:write',
            'comments:write',
        ],
        'member' => [
            'profile:read',
            'profile:write',
            'academy:read',
            'academy:write',
            'certificates:read',
            'certificates:write',
            'dialogue:read',
            'dialogue:write',
            'projects:read',
            'projects:write',
            'comments:write',
        ],
        'instructor' => [
            'profile:read',
            'profile:write',
            'academy:read',
            'dialogue:read',
            'projects:read',
            'comments:write',
        ],
    ],

    /** Used when a user has no mapped API abilities from roles. */
    'default_api_abilities' => [
        'profile:read',
        'profile:write',
    ],

];
