# Changelog

This changelog tracks **user-visible** changes across `backend/` (Laravel admin + API), `mobile/` (Expo RN), and `PWA/` (Vite React).

## Unreleased

### PWA (member web app)
- Shipped Progressive Web App under `/app/` (source `PWA/`, build → `backend/public/app/`).
- Full member flows: auth, home banners, constitution reader (offline IndexedDB), academy, library, party, chat, notifications, profile.
- Semantic **WorkflowIcon** system aligned with mobile keys (no raw Lucide at call sites).
- Honour `app-config` flags: `enable_dialogue`, realtime, webpush.
- Priority projects require sign-in; deep links use `GET /api/v1/priority-projects/{id}`.
- Lesson pages load course content when opened without navigation state.
- Web Push registration from Profile (VAPID); LAN-friendly Reverb host fallback for phones.

### API security (IDOR hardening)
- Push / Web Push registration rejects cross-account token/endpoint takeover (**409**).
- Dialogue message report always authorizes against the message thread.
- Liking an unpublished priority project returns **404** (not 403).
- See [API-SECURITY.md](./API-SECURITY.md).

### Priority projects API
- Added `GET /api/v1/priority-projects/{id}` (published only; Sanctum + `projects:read`).
- List/show/like remain authenticated (`projects:read` / `projects:write`).

### Realtime & ops
- Reverb Docker image installs PHP **pcntl** (fixes `SIGINT` crash on Alpine).
- PHPUnit forces `BROADCAST_CONNECTION=null` so tests never call Reverb.

### Setup Wizard (backend)
- Added public **6-step installation wizard** at `/setup` (Welcome → checks → admin → platform → seed → finish).
- System checks: PHP, extensions, DB, storage link, GD, mail, official PDF, queue.
- Platform step: Softaculous-style **Installation URL** (protocol / domain / directory) → `public_site_url` / recommended `APP_URL`.
- Mandatory content seed (constitution, academy, banners, library, static pages).
- Finish step: **Production checklist** (mail, CORS, cron, mobile API, certificates, admin invites) with copy buttons.
- Wizard progress restored from DB if session lost; `installed_at` locks wizard after complete.

### Admin dashboard (web)
- Added top-right **bell** + **gear** actions with a right-side settings drawer.
- Bell now shows a **live activity feed** driven by audit logs (enrolments, messages, membership grants, registrations).
- Bell now supports **unread counts** + **mark-as-read** on open (per admin user).
- Added a DB-backed **quick search** typeahead for Users, Courses, Sections, Library documents, and Certificates.
- Added footer credit: **© 2026, Created by TTM Group**.
- Added footer links to Privacy/Terms/Cookies/Help.

### Legal pages (web + mobile)
- Added public legal pages: **Privacy policy**, **Terms of use**, **Cookies**.
- Made public legal pages render from Admin-managed **Static Pages** (single source of truth).
- Added `cookies` to static pages seed data.

### Mobile app
- Added Privacy/Terms/Cookies to the in-app menu (opens Static Pages).
- Registration screen now links to the public legal pages.
- Continued migrating UI icons to semantic `WorkflowIcon` keys (and added guardrails to prevent direct Ionicons usage).

### Chat (Dialogue)
- Added support for media attachments end-to-end (storage + API + mobile rendering).
- Portal notifications + Web Push / Expo push channels for member alerts.

### Academy (LMS)
- Hardened admin flows with Form Requests + policy-based authorization across courses, assessments, questions, and badges.

