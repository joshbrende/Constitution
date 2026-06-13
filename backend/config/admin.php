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
        'analytics' => ['system_admin', 'content_editor', 'approver', 'presidium', 'analytics_viewer', 'stakeholder'],
        'audit_logs' => ['system_admin', 'presidium', 'audit_viewer'],
        'roles' => ['system_admin'], // Role CRUD - system admin only
    ],

    /*
    |--------------------------------------------------------------------------
    | Human-readable admin section labels (for role duty emails and invite UI)
    |--------------------------------------------------------------------------
    */
    'section_labels' => [
        'constitution' => 'Manage Constitution',
        'academy' => 'Manage Academy',
        'library' => 'Manage Digital Library',
        'party' => 'Manage the Party',
        'party_leagues' => 'Party Leagues',
        'presidium' => 'Manage Presidium',
        'party_organs' => 'Manage Party Organs',
        'priority_projects' => 'Priority Projects',
        'home_banners' => 'Home Banners',
        'static_pages' => 'Static Pages',
        'dialogue' => 'Opinion & Dialogue',
        'certificates' => 'Certificates',
        'users' => 'Users',
        'members' => 'Members',
        'analytics' => 'Analytics & Reports',
        'audit_logs' => 'Audit Logs',
        'roles' => 'Role Management',
    ],
];
