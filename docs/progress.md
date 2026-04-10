# Constitution App – Progress So Far

Documenting development progress for the ZANU PF Constitution app, the Constitution of Zimbabwe integration, Presidium management, Dialogue, and Priority Projects.

---

## Overview

- **Backend**: Laravel API and admin dashboard for constitutions, Presidium, Dialogue, Priority Projects, Party, Academy, Library
- **Web**: Dashboard, administration console, and constitution reader (ZANU PF and Zimbabwe)
- **Mobile**: React Native (Expo) app with dual-constitution reader, Presidium view, Dialogue (chat), Digital Library, Party Organs, Priority Projects, and Profile

---

## Completed Features

### Admin & Authentication

- **Admin login**: `admin@zanupf.org` / `Admin@2025!` (system_admin role)
- **AdminUserSeeder** integrated into DatabaseSeeder
- **Edit-everywhere**: Admins can edit Parts, Chapters, Sections, Academy content
- **Constitution reader**: Edit and Amendments buttons for admins
- **Direct body editing**: Section body editable on section edit page (not just via amendments)
- **Registration role hardening**:
  - Web registration now assigns `student` only.
  - `member` role is no longer granted at registration and remains tied to membership pass/certificate issuance.
- **Middleware/RBAC fixes**:
  - Restored intended admin-content access roles in `admin.content`.
  - Added missing `EnsurePresidiumAccess` middleware used by presidium-protected routes.

### Static pages (Help, Terms, Privacy, Settings)

- **Schema**: `static_pages` table stores slug, title, body, published flag.
- **Admin**: `Admin\StaticPagesController` under **Administration → Manage Help & legal pages** and **Admin & Oversight → Static pages**.
- **API**: `GET /api/v1/pages/{slug}` returns published pages for mobile (`help`, `terms`, `privacy`, `settings`).
- **Seeded content**: Detailed Help & Support, Terms & Conditions, and Privacy Policy. Settings is available as a managed page.

### Dual Constitution Support

#### Schema
- **Migration**: `constitution_slug` added to `chapters` table (default `zanupf`)
- Backfilled existing chapters with `zanupf`
- Both constitutions use same structure: Chapter → Section → SectionVersion

#### Documents
- **ZANU PF Constitution**: `constitution_slug: zanupf`, uses "Article" label
- **Constitution of Zimbabwe (2013)**: `constitution_slug: zimbabwe`, uses "Section" label

#### Routes & Controllers
- `/constitution/{doc?}/{section?}` – `doc` = `zanupf` | `zimbabwe`
- **WebConstitutionController**: Filters chapters by `constitution_slug`, passes `docMeta` for titles and labels
- Dashboard tiles and sidebar links for both constitutions
- Doc switcher in reader UI

### Constitution of Zimbabwe – Content

- **ZimbabweConstitutionSeeder**: Preamble + 18 chapters with structure
- **ImportZimbabweConstitution command**: Parses extracted PDF text and imports full content (334 sections)
  - Source: `storage/app/zimbabwe-constitution-source.txt`
  - Run: `php artisan constitution:import-zimbabwe`
  - Supports `--dry-run` and `--file=/path/to/custom.txt`
- Full Constitution of Zimbabwe (2013) with preamble and sections 1–332 imported
- DatabaseSeeder calls import automatically when source file exists

### Academy

- **Membership via exam** (design): Applicants must complete a course and pass an assessment to become members.
- **Membership course plan**: See [membership-course-plan.md](membership-course-plan.md) – course structure, modules/lessons from both constitutions, assessment design, pass→member flow, Admin registration.
- Terminology: "assessment" (not "quiz") for course completion.
- CRUD for courses and assessments in admin is in place; membership flags and flows are defined.
- **Membership course content (seeded)**:
  - 10 modules with 2–3 lessons each (ZANU PF Foundation, Membership, Structure, Leagues; Zimbabwe Values, Rights, Executive, Elections, Judiciary, Provincial & Local).
  - 107 assessment questions (10–12 per module), mix of True/False and MCQ, constitution-grounded.
  - Assessment: 25 questions per attempt, 70% pass mark, 45 minutes. Admin can edit all questions via Academy.
- **Eligibility gate**: Users must provide a Zimbabwe National ID number (`national_id`) before enrolling in a course or starting an assessment (enforced by API + mobile redirect to Profile).
- **Assessment anti-cheat hardening**:
  - Option correctness (`is_correct`) is hidden from learner-facing JSON responses.
  - Assessment question subsets are now bound to attempts:
    - `GET /api/v1/academy/assessments/{assessment}` returns a `question_set_token`.
    - `POST /api/v1/academy/assessments/{assessment}/attempts` consumes that token and stores exact `question_ids` on the attempt.
    - `POST /api/v1/academy/attempts/{attempt}/submit` validates that submitted answers match the exact question set shown.
  - Added `question_ids` JSON column to `assessment_attempts`.
- **Enrolment enforcement for assessments**:
  - Learners must be enrolled in the relevant course to access/start/submit assessments.
- **Badges criteria alignment**:
  - Admin badge rule options now match API logic:
    - `enrolled_n`, `completed_n`, `pass_score_at_least`,
    - `assessment_started_n`, `assessment_submitted_n`,
    - `membership_granted`, `certificate_issued`, `perfect_attempt`.
  - Membership-related badge evaluation now aligns with certificate issuance semantics.
- **Learner UX simplification (mobile)**:
  - Modules/lessons are kept for admin authoring/future expansion, but hidden from learner mobile flows.
  - Learners currently see course overview, enrolment state, and assessment path.
- **Result messaging (mobile)**:
  - Pass: congratulatory message + "View certificates".
  - Fail: "Not passed" message + retry guidance.

### Presidium (Leadership)

- **Schema & data**
  - `presidium_members` table with name, title, role_slug, bio, order, photo URL, and links to relevant ZANU PF and Zimbabwe constitution sections.
  - `PresidiumMember` model with `published()` and `ordered()` scopes.
  - `PresidiumSeeder` populates core Presidium roles (President, Vice Presidents, National Chairperson, Secretary-General) and links them to the constitutions where possible.
- **API**
  - `GET /api/v1/presidium` returns published members with constitutional references, consumed by the mobile app.
- **Admin**
  - `Admin\PresidiumAdminController` with full CRUD for Presidium members.
  - Admin pages under **Administration → Manage Presidium**:
    - List, create, edit, delete members.
    - Control ordering and published/draft status.
- **Web**
  - Dashboard tile for Presidium now points to internal content (Party page / Presidium section), rather than the external website.

### Dialogue & Chat

- **Schema**
  - `dialogue_channels`, `dialogue_threads`, `dialogue_messages`, `dialogue_thread_reads` tables.
  - Channels can be configured for Presidium, Leagues, and other dialogue spaces; thread read state tracks last-read timestamps per user.
- **Backend models**
  - `DialogueChannel`, `DialogueThread`, `DialogueMessage`, `DialogueThreadRead` with helpers for:
    - Determining if a user can post.
    - Counting unread messages per thread for a given user.
    - Tracking per-channel unread counts and whether there is an unread **official/editor** reply.
- **Admin Web**
  - `Admin\DialogueController` with:
    - Channel index and edit screen.
    - Thread list per channel, ability to start new topics.
    - Thread detail view with full message list, moderation tools (lock/unlock, pin/unpin, delete).
  - Dashboard and Admin tiles for **Dialogue** (Opinion & Dialogue; Manage Dialogue).
- **API**
  - Authenticated routes under `/api/v1/dialogue/...`:
    - `GET /dialogue/channels` – channels + constitutional links + unread counts + `has_official_reply`.
    - `GET/POST /dialogue/channels/{channel}/threads`.
    - `GET/POST /dialogue/threads/{thread}/messages`.
  - New messages can trigger database notifications (design in place for queueable notifications).
- **Mobile**
  - `ChatHomeScreen` – lists dialogue channels with:
    - Constitution pills (ZANU PF / Zimbabwe article references).
    - Unread badges on the right (e.g. `387+`).
    - Left icon turns **red with a subtle animation** when there is an unread official/editor reply, and returns to yellow once read.
  - `ChatChannelScreen` – lists threads/topics within a channel; shows creator (with “Editor” instead of “System”) and related constitutional references.
  - `ChatThreadScreen` – full chat thread view with:
    - Messages, authors (Editor vs member), timestamps.
    - Keyboard-aware layout on iOS and Android (input not covered by keyboard).
    - Ability to send new messages; thread read state updates so official icons revert to yellow after reading.

### Priority Projects

- **Schema**
  - `priority_projects` table:
    - Fields for title, slug, summary, body, image URL, likes count, published flags, and links to related ZANU PF / Zimbabwe constitutional sections.
  - `priority_project_likes` table:
    - Tracks which users have liked which projects (unique per user + project).
- **Backend**
  - `PriorityProject` and `PriorityProjectLike` models.
  - `PriorityProjectsSeeder` populates example Vision 2030-aligned projects and marks them published.
  - Admin controller `Admin\PriorityProjectsController` with full CRUD; admin sidebar entry **Manage Priority Projects**.
  - API controller `Api\PriorityProjectsController`:
    - `GET /api/v1/priority-projects` – list of published projects with likes and `liked` flag for the current user.
    - `POST /api/v1/priority-projects/{id}/like` – idempotent like endpoint; increments `likes_count` on first like.
- **Mobile**
  - `PriorityProjectsScreen`:
    - Lists all published projects with title, summary, optional image, truncated body, date, and like button.
    - Allows each signed-in user to **like** a project once (optimistic update + server sync).
  - `PriorityProjectDetailScreen`:
    - Full-screen detail view of a single project with full body text, date, image, and likes summary.
  - Home screen tile: **Priority projects** opens the internal list (no external website).

### Mobile App – Reader

#### Constitution List
- **Doc switcher**: ZANU PF | Zimbabwe tabs
- **Per-document covers**:
  - ZANU PF: `constitution-cover.png`
  - Zimbabwe: `constitution-cover-2.png`
- **API**: `/parts?doc=zanupf|zimbabwe` and `/sections/search?q=...&doc=...`
- Search scoped to selected constitution
- Correct titles: ZANU PF full title; "Constitution of the Republic of Zimbabwe (2013)"

#### Section & Chapter Screens
- **SectionDetailScreen**:
  - Document title: "Constitution of Zimbabwe" vs "ZANU PF Constitution" based on `doc`
  - Section label: "Section" vs "Article" for Zimbabwe vs ZANU PF
  - Share and PDF export use correct document title
  - TOC fetches `/parts?doc=...` for active constitution
  - `doc` passed when navigating prev/next
- **ChapterDetailScreen**: Section/Article label, passes `doc` to SectionDetail

#### Bookmarks & Highlights
- **ReaderDataContext**: Stores `constitution_slug` with bookmarks and text highlights
- **BookmarksScreen** / **HighlightsScreen**: Pass `doc` when navigating, show correct fallback label ("Section" vs "Article")
- Backward compatible: legacy entries get `constitution_slug: zanupf`

#### Search & PDF
- Search fallback: "Section" for Zimbabwe when no logical_number
- **PDF export**: Includes document title at top

### API Changes

- **PartController**: `?doc=zanupf|zimbabwe` filters chapters by constitution
- **SectionController**: Search accepts `?doc=...` and scopes to that constitution
- Section API response includes `chapter.constitution_slug` (used for doc detection when `doc` not in route params)
- **Academy API hardening**:
  - `academy/assessments/{assessment}` now requires authenticated, eligible, enrolled users and returns token-bound question sets.
  - `academy/assessments/{assessment}/attempts` supports `question_set_token` binding.
  - `academy/attempts/{attempt}/submit` validates question/option integrity and exact question-set submission.
  - Submission path now validates payload before writes and commits answers/status updates in a database transaction to prevent partial attempt corruption.
  - Added DB uniqueness guard for answers: one row per `(assessment_attempt_id, question_id)`.

### Mobile: Profile (Zimbabwe ID requirement)

- **Profile screen** now loads the authenticated user from `GET /api/v1/profile` and allows saving `national_id` via `PUT /api/v1/profile`.
- **Input assistance**: Auto-inserts a dash after the first 2 digits (e.g. `12-...`) to make typing the Zimbabwe ID format easier.
- **Course enforcement**:
  - Backend returns `422` with code `NATIONAL_ID_REQUIRED` when missing.
  - Mobile redirects users to Profile when they try to enrol or start an assessment without an ID.

### Home banners: internal navigation (no website redirects)

- **Banners CTA** now supports internal destinations:
  - `cta_type`: `internal | external`
  - `cta_tab`: `HomeTab | ConstitutionTab | ChatTab | ProfileTab`
  - `cta_screen`: optional nested screen (e.g. `PriorityProjects`, `AcademyHome`)
  - `cta_params`: optional JSON params
- **Admin**: Home banner form supports selecting internal CTA targets.
- **Mobile**: Banner taps navigate inside the app when `cta_type=internal`.

### Certificates: security hardening and operations

- **Verification hardening**:
  - Public verification now requires both certificate number (or public ID) and verification code.
  - Public verify endpoint is throttled (`throttle:certificate-verify`) with:
    - Per-IP cap.
    - Per-IP + identifier combo cap.
- **Public-safe identifiers**:
  - Added `public_id` (UUID) to certificates for non-sequential external reference.
  - Certificate numbers are now generated as non-sequential random-form IDs (`ZP-MEM-<year>-<random>`).
- **Signed verification token**:
  - Certificates include HMAC-signed verification token support.
  - QR verification URL now carries `id`, `number`, `code`, and signed `token`.
  - Verification endpoint validates signed token when provided.
- **Lifecycle status fields**:
  - Added `expires_at` and `revoked_at`.
  - Verification UI shows computed status: `active | expired | revoked`.
- **Admin operations**:
  - Added admin certificate actions to revoke/reinstate certificates.
  - Admin certificates table now shows status, expiry, and action controls.
  - Revoke now persists metadata:
    - `revoked_by_user_id`
    - `revoked_reason`
- **Audit logging infrastructure**:
  - Added `audit_logs` table, `AuditLog` model, and `AuditLogger` service.
  - Security-sensitive certificate actions now write audit events with actor, action, target, metadata, and request context.
- **Full audit coverage (enterprise readiness sprint)**:
  - Auth: `auth.api.registered`, `auth.api.logged_in`, `auth.api.login_failed`, `auth.api.logged_out`, `auth.api.refresh_succeeded`, `auth.api.refresh_failed`, `auth.api.password_reset_requested`, `auth.api.password_reset_rate_limited`.
  - Web auth: `auth.web.registered`, `auth.web.logged_in`, `auth.web.login_failed`, `auth.web.logged_out`.
  - Academy: `academy.attempt_started`, `academy.attempt_submitted`.
- **Default expiry policy**:
  - New config `config/certificates.php` with `default_expiry_days` (default 730).
  - Membership issuance now sets `expires_at` for new certificates (optional, configurable).

### Backend targeted test coverage (new)

- Added focused feature tests for recent security/correctness changes:
  - Web registration role assignment (`student` only).
  - Certificate admin search mode behavior.
  - Certificate revoke/reinstate metadata and audit log writes.
  - Assessment invalid option mapping with no partial writes.
  - Assessment answer uniqueness constraint behavior.
- Added/updated test harness files:
  - `tests/CreatesApplication.php`
  - `tests/TestCase.php`
- Current backend test run passes with the new coverage.

### Observability and ops (enterprise readiness sprint)

- **Request context middleware**:
  - Global `RequestContextMiddleware` injects/propagates `X-Request-Id` on every request.
  - Adds structured log context via `Log::withContext()`: `request_id`, `method`, `path`, `ip`, `user_id`.
  - Response headers include `X-Request-Id` for correlation.
- **Queue health check**:
  - `php artisan ops:queue-health` checks failed jobs count and stale pending/generating certificate PDFs.
  - Supports `--json` output for monitoring integration.
- **Security data cleanup**:
  - `php artisan ops:cleanup-security-data` prunes aged audit logs and expired/revoked refresh tokens.
  - Supports `--dry-run`.
  - Configurable via `config/operations.php` and env: `AUDIT_LOG_RETENTION_DAYS`, `REFRESH_TOKEN_RETENTION_DAYS`.
- **Scheduler**:
  - `ops:queue-health` every 5 minutes.
  - `ops:cleanup-security-data` daily at 02:15.

### Mobile: Certificates (revoked/expired status)

- **CertificatesScreen** now shows certificate lifecycle status:
  - Revoked certificates: muted styling, "· Revoked" label, ban icon; tap shows alert; download disabled.
  - Expired certificates: "· Expired" label; download still allowed (historical copy).
- **Backend**: `generate()` and `download()` return 403 for revoked certificates.
- Mobile error handling: 403 revoked responses show a clear "Revoked" alert.

### Mobile: Cleanup

- Removed unused `HomeTabs.js` (dead code).
- Added `mobile/.env.example` documenting `EXPO_PUBLIC_API_BASE_URL` for production builds.

### Input sanitization and validation

- **HTML sanitization**: `HtmlSanitizer` sanitizes party organ and library document body on output (views) and input (admin controllers). Uses mews/purifier when available; fallback with strip_tags + href cleaning.
- **URL validation**: `SafeUrlRule` restricts `image_url`, `cta_url`, `photo_url` to http/https schemes (Home Banners, Priority Projects, Presidium).
- **Certificate verification**: Query params `id`, `number`, `code`, `token` are trimmed and length-limited.
- **National ID**: `ZimbabweNationalIdRule` validates Zimbabwe ID format on profile update.
- **Static pages**: Body max length 50,000 characters.

### Health endpoint (load balancer)

- `GET /health` and `GET /api/v1/health` are **public** (no auth) for LB/uptime checks.
- Returns `{ "status": "ok" | "degraded", "checks": { "database", "redis" } }`; 503 when degraded.
- See [LOAD-BALANCER.md](LOAD-BALANCER.md).

### Mobile platform compatibility update

- Updated `expo-file-system` to match Expo SDK expectations:
  - `expo-file-system@~19.0.21` (from older `~18.x`), aligned with Expo 54 compatibility checks.

---

## File Structure (Key Paths)

```
backend/
  app/
    Rules/SafeUrlRule.php
    Rules/ZimbabweNationalIdRule.php
    Support/HtmlSanitizer.php
    Http/Controllers/CertificateVerificationController.php
    Http/Controllers/Admin/CertificatesController.php
    Http/Middleware/EnsureAdminOrContentEditor.php
    Http/Middleware/EnsurePresidiumAccess.php
    Http/Middleware/RequestContextMiddleware.php
    Models/AuditLog.php
    Models/Certificate.php
    Services/AuditLogger.php
    Services/CertificatePdfService.php
    Console/Commands/ImportZimbabweConstitution.php
    Console/Commands/QueueHealthCheckCommand.php
    Console/Commands/CleanupSecurityDataCommand.php
    Http/Controllers/PartController.php
    Http/Controllers/SectionController.php
    Http/Controllers/WebConstitutionController.php
  database/
    migrations/2026_03_19_120000_add_question_ids_to_assessment_attempts_table.php
    migrations/2026_03_19_130000_add_public_security_fields_to_certificates_table.php
    migrations/2026_03_19_140000_add_revocation_metadata_to_certificates_table.php
    migrations/2026_03_19_150000_add_unique_attempt_question_constraint_to_assessment_answers.php
    migrations/2026_03_19_160000_create_audit_logs_table.php
    migrations/..._add_constitution_slug_to_chapters_table.php
    seeders/ZimbabweConstitutionSeeder.php
    seeders/AdminUserSeeder.php
  config/operations.php
  routes/console.php
  storage/app/zimbabwe-constitution-source.txt   # Extracted PDF text

mobile/
  src/
    screens/ConstitutionListScreen.js
    screens/ChapterDetailScreen.js
    screens/SectionDetailScreen.js
    screens/AssessmentScreen.js
    screens/AssessmentResultScreen.js
    screens/CourseDetailScreen.js
    screens/BookmarksScreen.js
    screens/HighlightsScreen.js
    context/ReaderDataContext.js
  assets/
    constitution-cover.png      # ZANU PF
    constitution-cover-2.png    # Zimbabwe
```

### Test files (new)

```text
backend/tests/
  CreatesApplication.php
  Feature/RegistrationRolesTest.php
  Feature/CertificateAdminTest.php
  Feature/AcademyAssessmentSubmissionTest.php
```

---

## How to Run

### Backend
```bash
cd backend
php artisan migrate
php artisan db:seed
# If zimbabwe-constitution-source.txt exists, import runs automatically
# Or manually: php artisan constitution:import-zimbabwe
php artisan serve --host=0.0.0.0
```

### Mobile
```bash
cd mobile
npm install
npx expo start
```
Update `api/client.js` base URL to your backend (e.g. LAN IP).

---

## Docs index

- [progress.md](progress.md) — this file
- [AUDIT-LOGGING.md](AUDIT-LOGGING.md) — audit events, query examples, retention
- [CERTIFICATE-SECURITY.md](CERTIFICATE-SECURITY.md) — certificate verification and admin runbook
- [INPUT-SANITIZATION.md](INPUT-SANITIZATION.md) — HTML sanitization, URL validation, national ID, certificate verification
- [OPS-RUNBOOK.md](OPS-RUNBOOK.md) — `ops:queue-health`, `ops:cleanup-security-data`, scheduler, env vars

---

## Environment notes (WAMP vs Docker)

- **Local (WAMP)**: `backend/.env` is configured for local development (DB host `127.0.0.1`). Session/cache/queue should be `database` unless you run Redis locally.
- **Docker**: use `backend/.env.docker` (or copy it to `backend/.env` for containers). Docker expects DB host `db` and Redis host `redis`.

---

## Performance & Loading

- **Mobile splash & bootstrap**
  - Custom splash screen that:
    - Attempts to restore the saved auth token from secure storage.
    - If a valid token exists, pre-warms key APIs (priority projects and dialogue channels) before entering the main app.
  - Falls back to Login screen when no token or errors occur.
- **Section caching**
  - `sectionCache` module stores section responses in AsyncStorage and serves cached content when offline or when API calls fail.
  - Cache keys are rotated to cap storage and keep lookups fast.
- **Design for backend load smoothing**
  - See [SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md](SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md) for the broader supply–demand strategy (queues, caching, rate limits, real-time feedback).

---

## Pending / Future Work

- Full content seeding for the **Membership Course** (modules, lessons, assessment question bank) and end‑to‑end “pass → membership role” flow.
- Deeper **offline sync** for constitution content, library documents, Presidium, Dialogue, and Priority Projects.
- Push notifications for amendments, Dialogue mentions, and new Priority Projects.
- Multi-language summaries (Shona, Ndebele).
- Gamification (badges, learning paths).
- AI-powered Q&A grounded in constitution text.

---

*Last updated: March 2026 (current)*
