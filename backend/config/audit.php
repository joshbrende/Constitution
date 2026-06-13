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

];
