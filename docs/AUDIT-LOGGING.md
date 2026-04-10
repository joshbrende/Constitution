# Audit Logging Operational Reference

This document explains how audit logging works, how to query it, and how to manage retention for operations/security.

---

## What is logged

Audit events are stored in `audit_logs` with:

- `actor_user_id`: authenticated user who performed the action
- `action`: event key (for example `certificate.revoked`)
- `target_type`, `target_id`: model/entity affected
- `metadata`: JSON details (reason, certificate number, etc.)
- `ip_address`, `user_agent`, `request_id`
- `created_at`

Current wired events include:

**Certificates**
- `certificate.revoked`
- `certificate.reinstated`

**Auth (API)**
- `auth.api.registered`
- `auth.api.logged_in`
- `auth.api.login_failed`
- `auth.api.logged_out`
- `auth.api.refresh_succeeded`
- `auth.api.refresh_failed`
- `auth.api.password_reset_requested`
- `auth.api.password_reset_rate_limited`

**Auth (Web)**
- `auth.web.registered`
- `auth.web.logged_in`
- `auth.web.login_failed`
- `auth.web.logged_out`

**Academy**
- `academy.attempt_started`
- `academy.attempt_submitted`

---

## Query examples

### 1) Latest 100 security-related actions

```sql
SELECT id, actor_user_id, action, target_type, target_id, ip_address, created_at
FROM audit_logs
WHERE action IN ('certificate.revoked', 'certificate.reinstated')
ORDER BY created_at DESC
LIMIT 100;
```

### 2) Show all events for one certificate record

```sql
SELECT id, action, metadata, actor_user_id, created_at
FROM audit_logs
WHERE target_type = 'App\\Models\\Certificate'
  AND target_id = 123
ORDER BY created_at DESC;
```

### 3) Events by one admin in a date range

```sql
SELECT id, action, target_type, target_id, metadata, created_at
FROM audit_logs
WHERE actor_user_id = 5
  AND created_at >= '2026-03-01'
  AND created_at <  '2026-04-01'
ORDER BY created_at DESC;
```

### 4) Find revocations with reason text

```sql
SELECT id, actor_user_id, JSON_EXTRACT(metadata, '$.reason') AS reason, created_at
FROM audit_logs
WHERE action = 'certificate.revoked'
ORDER BY created_at DESC;
```

> Note: JSON functions can vary by database engine/version. For MySQL 8+, `JSON_EXTRACT` is supported.

---

## Laravel/Tinker query examples

```php
use App\Models\AuditLog;

// Latest revokes/reinstatements
AuditLog::whereIn('action', ['certificate.revoked', 'certificate.reinstated'])
    ->latest()
    ->limit(100)
    ->get();

// Events for one certificate ID
AuditLog::where('target_type', App\Models\Certificate::class)
    ->where('target_id', 123)
    ->latest()
    ->get();
```

---

## Retention guidance

Recommended baseline:

- **Hot retention (DB):** 12 months
- **Archive retention (cold storage):** 24-36 months (policy-dependent)

Suggested policy by environment:

- **Production:** 12 months in DB + monthly archive export
- **Staging:** 90 days
- **Local/dev:** 30 days (or lower as needed)

---

## Purge/archival procedure

1. Export records older than retention threshold to secure archive (CSV/JSON dump).
2. Verify archive integrity and access controls.
3. Delete archived rows from primary DB.
4. Record purge run details (time, range, row count, operator).

Example SQL purge (after archive):

```sql
DELETE FROM audit_logs
WHERE created_at < NOW() - INTERVAL 12 MONTH;
```

---

## Operational checks

Run these checks monthly:

- Ensure audit volume is non-zero for expected admin actions.
- Verify `actor_user_id` is present for authenticated admin events.
- Spot-check that revoke reason metadata is present where expected.
- Confirm time sync/timezone consistency in timestamps.
- Confirm backup and restore procedures include `audit_logs`.

---

## Security notes

- Avoid storing secrets, passwords, or full tokens in `metadata`.
- Keep metadata concise and purpose-specific (reason, IDs, high-level context).
- Restrict direct DB access to audit table in production.
- Treat audit exports as sensitive operational records.

