<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin section access by role
    |--------------------------------------------------------------------------
    | Maps admin sections to role slugs that can access them.
    | system_admin, content_editor, approver, presidium have full access to all.
    */
    'sections' => [
        'constitution' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'academy' => ['system_admin', 'content_editor', 'approver', 'presidium', 'academy_manager'],
        'library' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'party' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'party_leagues' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'presidium' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'party_organs' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'priority_projects' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'home_banners' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'static_pages' => ['system_admin', 'content_editor', 'approver', 'presidium'],
        'dialogue' => ['system_admin', 'content_editor', 'approver', 'presidium', 'dialogue_moderator', 'moderator'],
        'certificates' => ['system_admin', 'content_editor', 'approver', 'presidium', 'user_manager', 'academy_manager'],
        'users' => ['system_admin', 'content_editor', 'approver', 'presidium', 'user_manager', 'provincial_admin'],
        'members' => ['system_admin', 'content_editor', 'approver', 'presidium', 'user_manager', 'provincial_admin'],
        'analytics' => ['system_admin', 'content_editor', 'approver', 'presidium', 'analytics_viewer'],
        'audit_logs' => ['system_admin', 'presidium', 'audit_viewer'],
        'roles' => ['system_admin'], // Role CRUD - system admin only
    ],
];
