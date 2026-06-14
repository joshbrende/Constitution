# Changelog

This changelog tracks **user-visible** changes across `backend/` (Laravel admin + API) and `mobile/` (Expo RN).

## Unreleased

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

### Academy (LMS)
- Hardened admin flows with Form Requests + policy-based authorization across courses, assessments, questions, and badges.

