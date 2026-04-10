# Role workflows & execution

## Constitution amendments (Content Editor → Presidium)

This is **implemented** in the application:

1. **Draft** — A `SectionVersion` is created or updated with `status = draft`.
2. **Submit** — A user with constitution access uses **Submit for Presidium approval**; status becomes `in_review`.
3. **Approve / Reject** — Only users with **Presidium** or **System Admin** role can approve or reject (`EnsurePresidiumAccess` on approve/reject routes). Approval sets the version to `published` and adjusts effective dates.

**Where in the UI:** Admin → Manage Constitution → select Part/Chapter/Section → **Amendments** (versions list).

The **Overview** dashboard now shows:
- Counts: drafts vs in-review (for users with constitution admin access).
- **Your responsibilities** cards: step-by-step text per role from `config/role_workflows.php`.

## Other roles

- **Academy Manager** — Courses/assessments only; no amendment approval.
- **Dialogue Moderator** — Dialogue admin only.
- **User Manager** — Users → Edit → assign roles; no content approval.
- **Analytics Viewer** — Read-only analytics and exports.
- **Audit Viewer** — Audit logs only.
- **Provincial Admin** — Same user/member tools as configured; province-scoped views can be added later.

## Audit trail (constitution & channels)

**Approvals without Presidium review** are traceable when they use the app:

| Action | Meaning |
|--------|--------|
| `constitution.version_submitted_for_review` | Draft → in review (proper handoff to Presidium). |
| `constitution.version_approved` | in review → published; metadata `presidium_review_bypassed: false`, `workflow_channel: presidium_review`. |
| `constitution.version_rejected_to_draft` | Returned to editor. |
| `constitution.section_published_direct` | **Bypass:** published via section editor “Publish now” (only Presidium/System Admin). Metadata includes `presidium_review_bypassed: true`, `workflow_channel: direct_publish`. |

Logs are stored in **`audit_logs`** (actor, IP, user agent, JSON metadata). **Admin → Audit logs** shows workflow channel and bypass flags.

**Limits:** Changes made only in the database or outside this app are not logged here. Retention is controlled by `AUDIT_LOG_RETENTION_DAYS` (see `config/operations.php`).

## Customising copy

Edit **`config/role_workflows.php`** for dashboard step text.  
Edit **`config/admin.php`** for which roles access which admin sections.
