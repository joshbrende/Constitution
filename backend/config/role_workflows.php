<?php

/**
 * Professional workflow steps shown on the Overview dashboard per role.
 * Execution: constitution amendments use SectionVersion statuses (draft → in_review → published).
 * See Admin Constitution → Amendments for approve/reject (Presidium / System Admin).
 */
return [
    'system_admin' => [
        'title' => 'System administrator',
        'summary' => 'Full technical and access control. Only you can create roles and assign any role, including Presidium and System Admin.',
        'steps' => [
            'Assign roles: Open Admin → Users. Find the user (search by name or email). Click their name to open Edit. Tick the roles you want (Academy Manager, Content Editor, User Manager, etc.). Click Update roles. Changes apply immediately.',
            'Create roles: Admin → Roles → Add role. Enter name, slug (e.g. academy_manager), and description. Add the new slug to config/admin.php under the sections the role should access.',
            'Monitor Audit logs (Admin → Audit logs) and system health; support Presidium and editors.',
            'Constitution: you may approve amendments like Presidium; prefer Presidium for political sign-off.',
        ],
    ],
    'content_editor' => [
        'title' => 'Content editor',
        'summary' => 'You prepare drafts. Presidium approves constitutional amendments before publication.',
        'steps' => [
            'Edit sections in Admin → Manage Constitution → open a section → Edit.',
            'Save work as draft, or use Amendments to create/version text.',
            'When ready, open Amendments for that section and Submit for Presidium approval (status becomes “in review”).',
            'Do not publish constitutional amendments yourself unless your organisation also assigns you Approver/Presidium rights.',
            'Presidium (or System Admin) approves or rejects; approved versions become published.',
        ],
    ],
    'approver' => [
        'title' => 'Approver',
        'summary' => 'You may publish non-amendment content where policy allows; constitutional amendments follow Presidium approval.',
        'steps' => [
            'Coordinate with Content Editors on readiness of drafts.',
            'Use Admin areas you are granted (library, static pages, etc.) per your assignment.',
            'For ZANU PF constitution text changes, ensure Presidium workflow is followed (in review → approved).',
        ],
    ],
    'presidium' => [
        'title' => 'Presidium',
        'summary' => 'You approve or reject constitutional amendments submitted for review.',
        'steps' => [
            'Open Admin → Manage Constitution; pending items are highlighted when versions await approval.',
            'Go to a section → Amendments → approve or reject versions in “in review” status.',
            'Approval publishes the amendment and sets effective dates; rejection returns control to editors.',
        ],
    ],
    'academy_manager' => [
        'title' => 'Academy manager',
        'summary' => 'Manage learning content and assessments only.',
        'steps' => [
            'Admin → Manage Academy: courses, modules, assessments, questions, badges.',
            'Publish courses when content is final; align pass marks with party policy.',
            'No access to constitution text or Presidium approval flows.',
        ],
    ],
    'dialogue_moderator' => [
        'title' => 'Dialogue moderator',
        'summary' => 'Moderate channels and threads professionally.',
        'steps' => [
            'Admin → Dialogue: review threads, pin official replies, lock if needed.',
            'Remove messages that breach policy; record reasons consistently.',
            'Escalate systemic issues to System Admin or Presidium as required.',
        ],
    ],
    'moderator' => [
        'title' => 'Moderator',
        'summary' => 'Same moderation scope as Dialogue Moderator unless restricted by policy.',
        'steps' => [
            'Use Admin → Dialogue to moderate channels and threads.',
            'Follow party communication standards in all actions.',
        ],
    ],
    'user_manager' => [
        'title' => 'User manager',
        'summary' => 'Assign roles; you do not edit constitutional or academy content.',
        'steps' => [
            'Admin → Users: find a user → Edit → assign roles appropriate to their duties.',
            'Only System Admin should assign System Admin or Presidium unless policy says otherwise.',
            'Members list shows certificate holders; use for membership verification context.',
        ],
    ],
    'analytics_viewer' => [
        'title' => 'Analytics viewer',
        'summary' => 'Read-only reporting.',
        'steps' => [
            'Admin → Analytics & reports: review metrics and export CSV where permitted.',
            'Do not share raw exports outside authorised channels.',
        ],
    ],
    'provincial_admin' => [
        'title' => 'Provincial administrator',
        'summary' => 'User and member oversight aligned to province (full province scoping can be enabled later).',
        'steps' => [
            'Use Users / Members as assigned; coordinate with national User Managers.',
            'Follow data protection rules for provincial member data.',
        ],
    ],
    'audit_viewer' => [
        'title' => 'Audit viewer',
        'summary' => 'Compliance and oversight of system actions.',
        'steps' => [
            'Admin → Audit logs: review authentication, academy, and certificate-related events.',
            'Use filters; retain findings according to your organisation’s retention policy.',
        ],
    ],
    'instructor' => [
        'title' => 'Instructor',
        'summary' => 'Academy teaching role when activated by policy.',
        'steps' => [
            'Support learners and course quality as directed by Academy management.',
        ],
    ],
    'member' => [
        'title' => 'Member',
        'summary' => 'Party member with app access.',
        'steps' => [
            'Complete mandatory Academy pathways and keep profile details current.',
        ],
    ],
    'student' => [
        'title' => 'Student',
        'summary' => 'Default learner account.',
        'steps' => [
            'Use Academy and constitution readers to study; complete assessments to progress.',
        ],
    ],
];
