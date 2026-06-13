# RBAC matrix (architecture reference)

Unified view of **role × surface × data type × action** for the Constitution platform.  
Source of truth for admin sections: `backend/config/admin.php`. API rules: `backend/app/Policies/*`.

**Phase 1 (2026):** Dialogue read access aligned with channel rules; API route binding scoped to resource owner for certificates and assessment attempts; meta-audit on audit log views and admin PII screens.

**Phase 2 (2026):** DB-backed `permissions` + `permission_role` tables; admin section/action slugs synced from config; Sanctum tokens issued with scoped abilities (not `['*']`); API routes enforce `abilities:*` middleware.

**Phase 3 (2026):** `provincial_admin` scoped to `users.province_id` on Users, Members, and quick search; global override for `system_admin`, `user_manager`, `presidium`.

**Phase 4 (2026):** Append-only audit model; SHA-256 hash chain; JSONL archive-before-purge; optional separate `audit` DB connection; `audit:export`, `audit:verify`, `permissions:sync` commands.

**Backend provisioning (2026):** System Administrator only may invite or create backend dashboard staff with scoped roles; welcome/invitation emails include login URL, duties, and admin areas from `BackendRoleDutiesService` + `config/role_workflows.php`.

---

## 0. Permission model (Phase 2)

| Domain | Slug pattern | Example |
|--------|--------------|---------|
| Admin section | `admin.section.{section}` | `admin.section.constitution` |
| Admin action | `admin.action.{action}` | `admin.action.presidium_publish` |
| API (Sanctum) | `{resource}:{action}` | `profile:read`, `dialogue:write` |

Sync: `PermissionSeeder` / `PermissionSyncService` reads `config/admin.php` + `config/permissions.php`.  
Token abilities: `TokenAbilityService` unions API permissions from the user's roles.

---

## 1. Planes

| Plane | Auth | Authorization mechanism |
|-------|------|-------------------------|
| **Web admin** | Session (`web` guard) | `admin.content` + `admin.section` middleware, Gates, `config/admin.php` |
| **Mobile / API** | Sanctum bearer (`auth:sanctum`) | Laravel Policies + query scoping (`user_id`) |
| **Public web/API** | None / optional Sanctum | Published content + `LibraryDocumentPolicy` |

There is **province scoping for `provincial_admin`** on Users / Members admin (Phase 3). Other admin sections remain national. District/branch scoping is not implemented yet.

---

## 0b. Geographic admin scope (Phase 3)

| Actor | Users / Members visibility |
|-------|----------------------------|
| `provincial_admin` (only) | Users where `province_id` matches admin's `province_id` |
| `provincial_admin` + `user_manager` / `system_admin` / `presidium` | Global (override) |
| `provincial_admin` without `province_id` set | Empty lists; cannot edit users |

Implementation: `AdminScopeService` + `config/scoping.php`. Provincial admins cannot invite backend users or assign roles with any admin section access.

---

## 0c. Backend user provisioning

| Action | Who | Mechanism |
|--------|-----|-----------|
| Invite backend user (email + activation link) | `system_admin` only | `UsersController::storeInvitation`, `BackendUserInvitationNotification` |
| Create backend user (immediate account + temp password) | `system_admin` only | `UsersController::storeBackendUser`, `BackendUserWelcomeNotification` |
| Assign roles on existing user | `system_admin`, `user_manager` | `UsersController::update` — user_manager cannot assign `system_admin` / `presidium` |
| Accept invitation | Guest (signed token) | `BackendUserInvitationController` |

**Provisionable roles:** only roles that appear in at least one `config/admin.php` section (`BackendRoleDutiesService::provisionableRoles`). Assigning e.g. `dialogue_moderator` grants Dialogue admin only — not full super-admin unless `system_admin` is explicitly selected.

**Duty content in emails:** `role_workflows.php` summaries/steps + `admin.section_labels` for “Admin areas”.

**Forbidden:** `user_manager`, `provincial_admin`, and all other non–system-admin roles receive 403 on invite/create routes (see `BackendUserInvitationTest`, `Phase3ProvincialAdminScopeTest`).

---

## 2. Admin sections × roles

| Section | Roles with access |
|---------|-------------------|
| constitution | system_admin, content_editor, approver, presidium |
| academy | + academy_manager |
| library, party, party_leagues, presidium, party_organs, priority_projects, home_banners, static_pages | system_admin, content_editor, approver, presidium |
| dialogue | + dialogue_moderator, moderator |
| certificates | + user_manager, academy_manager, **provincial_admin** |
| users, members | + user_manager, provincial_admin |
| analytics | + analytics_viewer, stakeholder |
| audit_logs | system_admin, presidium, audit_viewer |
| roles (CRUD) | system_admin only |

**Extra action gates (not section slugs):**

| Action | Gate / middleware | Roles |
|--------|-------------------|-------|
| Amendment approve/reject | `presidium` middleware | presidium, system_admin |
| Platform settings | `hasRole('system_admin')` in controller | system_admin only |
| Quick search / FAQ submit | `admin.anyAccess` | Any role with ≥1 admin section |
| Quick search result groups | Filtered by caller's accessible sections | Per section map in `AdminQuickSearchController` |
| Confirm academy payment | `admin.action.academy_payment_confirm` | academy_manager, system_admin, provincial_admin |
| Presidium approve certificate | `admin.action.academy_certificate_presidium_approve` | presidium, system_admin |
| Print / download certificate PDF (admin) | `admin.action.academy_certificate_print` | academy_manager, system_admin |
| Mark ready / collected | `admin.action.academy_certificate_collection` | academy_manager, system_admin, provincial_admin |

**Academy certificate workflow:** Provincial admins see applications for their province only (`AdminScopeService`). Presidium cannot confirm payment. Students use `/api/v1/academy/applications` — not certificate PDF download. See [`ACADEMY-CERTIFICATE-WORKFLOW.md`](./ACADEMY-CERTIFICATE-WORKFLOW.md).

---

## 3. API resources × policy × actions

| Resource | Policy | view / read | create / write | Notes |
|----------|--------|-------------|----------------|-------|
| Library document | `LibraryDocumentPolicy` | `view` by `access_rule`: public / member (auth) / leadership (presidium, system_admin) | N/A (admin CMS) | List filtered in controller |
| Certificate | `CertificatePolicy` | Owner list only (`user_id`) when legacy download enabled | `generate`, `download` — **denied** when `academy.student_certificate_download_enabled` is false | Government workflow: use `CertificateApplication` API |
| Certificate application | `CertificateApplicationPolicy` | Owner only (`user_id`) | Receipt PDF download — owner only | Created on exam pass; see applications API |
| Course | `CoursePolicy` | Published courses public to auth users | `enrol` — published only | National ID in controller |
| Assessment | `AssessmentPolicy` | `take` — enrolled + published | start/submit via attempts | Anti-cheat in controller |
| Assessment attempt | `AssessmentAttemptPolicy` | — | `submit` — owner + in_progress + enrolled | Route binding scoped on API |
| Dialogue channel | `DialogueChannelPolicy` | `view` — `min_role_slug` or open | `createThread` — same as view | Channel list filtered |
| Dialogue thread | `DialogueThreadPolicy` | `view` — via channel access | `reply` — open thread + channel post rule | |
| Profile / user | `UserPolicy` | Self only | `update`, `logoutApi` — self only | |
| Priority project | `PriorityProjectPolicy` | Published index | `like` — published only | |

**Sanctum tokens:** scoped abilities from `TokenAbilityService` (e.g. `profile:read`, `academy:write`). Route groups in `routes/api.php` enforce `abilities:*` middleware. Wildcard `['*']` is no longer issued on login/register/refresh.

---

## 4. User-owned data isolation

| Data | Isolation mechanism |
|------|---------------------|
| Profile | `UserPolicy` — self only |
| Enrolments, attempts, badges, certificates (list) | Queries scoped with `where('user_id', auth id)` |
| Certificate / attempt (by ID) | `resolveRouteBinding` → 404 if not owner on API paths |
| Dialogue | Channel `min_role_slug`; not per-user private threads |
| Constitution, party, library (public) | Intentionally shared |

---

## 5. Audit-sensitive admin actions

| Event | When |
|-------|------|
| `admin.users.pii_viewed` | Admin opens user role edit screen |
| `admin.users.roles_updated` | Admin saves role changes |
| `admin.users.invitation_sent` | System admin sends backend invite |
| `admin.users.backend_created` | System admin creates backend user with welcome email |
| `admin.platform_settings.updated` | System admin saves platform settings |
| `audit_logs.viewed` | Admin opens audit log index (meta-audit) |

Full event list: [`AUDIT-LOGGING.md`](./AUDIT-LOGGING.md).

---

## 6. Audit store (Phase 4)

| Control | Implementation |
|---------|----------------|
| Append-only | `AuditLog` model blocks update/delete; purge uses `AuditLog::allowingMutation()` |
| Integrity | SHA-256 chain (`integrity_hash`, `previous_hash`) via `AuditIntegrityService` |
| Archive before purge | `AuditArchiveService` → `storage/app/audit-archives/` JSONL |
| Separate store | Optional `AUDIT_DB_CONNECTION=audit` (see `config/audit.php`) |
| Export / verify | `audit:export`, `audit:verify`; admin UI export logs `audit_logs.exported` |

---

## 7. Related docs

- [`backend-manual/04-admin-rbac.md`](./backend-manual/04-admin-rbac.md) — middleware detail
- [`backend-manual/05-roles-users.md`](./backend-manual/05-roles-users.md) — role assignment
- [`AUDIT-LOGGING.md`](./AUDIT-LOGGING.md) — audit operations
- [`ACADEMY-CERTIFICATE-WORKFLOW.md`](./ACADEMY-CERTIFICATE-WORKFLOW.md) — government payment + Presidium certificate process
- [`CERTIFICATE-SECURITY.md`](./CERTIFICATE-SECURITY.md) — certificate verification

*Update this matrix when adding roles, admin sections, or API policies.*
