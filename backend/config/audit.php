<?php

/**
 * Audit log infrastructure (Phase 4 — SOC 2-oriented controls).
 *
 * By default the audit store uses the same DB connection as the app.
 * Set AUDIT_DB_CONNECTION=audit (and AUDIT_DB_* credentials) for a separate database.
 */
return [

    /** Null = use default application DB connection. */
    'connection' => env('AUDIT_DB_CONNECTION'),

    'integrity' => [
        /** SHA-256 hash chain links each row to the previous audit entry. */
        'enabled' => env('AUDIT_INTEGRITY_ENABLED', true),
    ],

    'archive' => [
        /** Require JSONL export before ops:cleanup-security-data deletes aged rows. */
        'require_before_purge' => env('AUDIT_REQUIRE_ARCHIVE_BEFORE_PURGE', true),
        /** storage/app/{path}/YYYY-MM-DD/batch-*.jsonl */
        'path' => env('AUDIT_ARCHIVE_PATH', 'audit-archives'),
    ],

    /**
     * Admin audit log UI — category filters (action prefix).
     *
     * @var array<string, string>
     */
    'categories' => [
        'auth' => 'auth.',
        'admin' => 'admin.',
        'academy' => 'academy.',
        'membership' => 'membership.',
        'certificate' => 'certificate.',
        'constitution' => 'constitution.',
        'dialogue' => 'dialogue.',
        'oversight' => 'audit_logs.',
    ],

    /** @var array<string, string> */
    'category_labels' => [
        'auth' => 'Authentication',
        'admin' => 'Administration',
        'academy' => 'Academy',
        'membership' => 'Membership',
        'certificate' => 'Certificates',
        'constitution' => 'Constitution',
        'dialogue' => 'Dialogue',
        'oversight' => 'Oversight',
    ],

    /**
     * Human-readable labels for audit actions (admin UI).
     *
     * @var array<string, string>
     */
    'action_labels' => [
        'auth.api.registered' => 'Mobile/API registration',
        'auth.api.logged_in' => 'Mobile/API sign-in',
        'auth.api.login_failed' => 'Mobile/API sign-in failed',
        'auth.api.logged_out' => 'Mobile/API sign-out',
        'auth.api.refresh_succeeded' => 'Mobile/API token refresh',
        'auth.api.refresh_failed' => 'Mobile/API token refresh failed',
        'auth.api.password_reset_requested' => 'Password reset requested',
        'auth.api.password_reset_rate_limited' => 'Password reset rate limited',
        'auth.web.registered' => 'Web registration',
        'auth.web.logged_in' => 'Web sign-in',
        'auth.web.login_failed' => 'Web sign-in failed',
        'auth.web.logged_out' => 'Web sign-out',
        'auth.backend_invitation.accepted' => 'Backend invitation accepted',
        'admin.users.invitation_sent' => 'Backend user invited',
        'admin.users.backend_created' => 'Backend user created',
        'admin.users.pii_viewed' => 'User profile viewed (PII)',
        'admin.users.roles_updated' => 'User roles updated',
        'admin.users.profile_updated' => 'User party profile updated',
        'admin.users.membership_standing_updated' => 'Membership standing changed',
        'admin.users.branch_admission_confirmed' => 'Branch admission confirmed',
        'admin.users.branch_admission_revoked' => 'Branch admission revoked',
        'admin.users.cadre_designated' => 'Cadre designee assigned',
        'admin.users.cadre_designation_revoked' => 'Cadre designation revoked',
        'admin.platform_settings.updated' => 'Platform settings updated',
        'academy.enrolled' => 'Course enrolment',
        'academy.attempt_started' => 'Assessment attempt started',
        'academy.attempt_submitted' => 'Assessment submitted',
        'academy.application.created' => 'Certificate application created',
        'academy.application.payment_confirmed' => 'Certificate payment confirmed',
        'academy.application.presidium_approved' => 'Certificate Presidium approved',
        'academy.application.printed' => 'Certificate marked printed',
        'academy.application.ready_for_collection' => 'Certificate ready for collection',
        'academy.application.collected' => 'Certificate collected',
        'membership.granted' => 'Member role granted',
        'membership.suspended' => 'Membership suspended',
        'membership.reinstated' => 'Membership reinstated',
        'membership.standing_changed' => 'Membership standing changed',
        'certificate.revoked' => 'Certificate revoked',
        'certificate.reinstated' => 'Certificate reinstated',
        'constitution.amendment_official_pdf_uploaded' => 'Amendment PDF uploaded',
        'constitution.section_published_direct' => 'Section published directly',
        'constitution.version_submitted_for_review' => 'Version submitted for review',
        'constitution.version_approved' => 'Version approved',
        'constitution.version_rejected_to_draft' => 'Version rejected to draft',
        'dialogue.message_sent' => 'Dialogue message posted',
        'audit_logs.viewed' => 'Audit log viewed',
        'audit_logs.exported' => 'Audit log exported',
        'audit_logs.purged' => 'Audit log retention purge',
    ],

    /**
     * Metadata keys → readable labels for the admin UI.
     *
     * @var array<string, string>
     */
    'metadata_labels' => [
        'email' => 'Email',
        'source' => 'Source',
        'reason' => 'Reason',
        'filters' => 'Filters',
        'row_count' => 'Rows exported',
        'archive_path' => 'Archive file',
        'refresh_token_id' => 'Refresh token ID',
        'course_title' => 'Course',
        'course_id' => 'Course ID',
        'application_id' => 'Application ID',
        'receipt_number' => 'Receipt number',
        'certificate_number' => 'Certificate number',
        'workflow_channel' => 'Workflow channel',
        'presidium_review_bypassed' => 'Presidium review bypassed',
        'from' => 'Previous standing',
        'to' => 'New standing',
        'admin_user_id' => 'Admin user ID',
        'before' => 'Before',
        'after' => 'After',
    ],

];
