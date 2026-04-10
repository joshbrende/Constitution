# 17. Audit logs (admin)

## 17.1 Purpose

Read-only list of **security and workflow events**: auth, academy attempts, certificates, **constitution amendment workflow**, etc.

## 17.2 Route

- `admin.audit-logs.index` — filter by `action` query parameter

**Controller:** `App\Http\Controllers\Admin\AuditLogsController`

## 17.3 Constitution events (metadata)

| Action | Meaning |
|--------|---------|
| `constitution.version_submitted_for_review` | Draft → in review |
| `constitution.version_approved` | Presidium review → published (`presidium_review_bypassed: false`) |
| `constitution.version_rejected_to_draft` | Returned to editors |
| `constitution.section_published_direct` | **Bypass** of review queue (`presidium_review_bypassed: true`, `workflow_channel: direct_publish`) |

UI shows **Workflow / details** column with channel and badges.

## 17.4 Full event reference

See **[`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md)** — retention, SQL examples, auth and academy action keys. Extend that document when adding new `AuditLogger` calls.

## 17.5 Retention

`config/operations.php` — `AUDIT_LOG_RETENTION_DAYS`; `CleanupSecurityDataCommand` prunes old rows.

---

*Last reviewed: documentation generation pass.*
