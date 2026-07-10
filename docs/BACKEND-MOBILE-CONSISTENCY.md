# Backend ↔ mobile / PWA: consistency, UX, performance

Step-by-step review of **`backend/`** (Laravel API + web), **`mobile/`** (Expo), and **`PWA/`** (Vite React at `/app/`). Use with [`backend-manual/20-api-overview.md`](./backend-manual/20-api-overview.md), [`ENVIRONMENTS.md`](./ENVIRONMENTS.md), [`PWA.md`](./PWA.md), and [`mobile/docs/OFFLINE-MOBILE.md`](../mobile/docs/OFFLINE-MOBILE.md).

---

## 1. API surface alignment

Mobile uses `axios` with `baseURL` = `{origin}/api/v1` (see `EXPO_PUBLIC_API_BASE_URL`). PWA uses the same paths with `VITE_API_BASE_URL` (default `/api/v1`). Relative paths below are under `/api/v1`.

| Area | Backend route(s) | Mobile module | PWA module | Notes |
|------|------------------|---------------|------------|--------|
| Auth | `POST auth/register`, `login`, `refresh`, `forgot-password`; `POST auth/logout` | `client.js` + screens | `LoginPage`, `authStorage`, `client.js` | Refresh + 401 handling in interceptor. |
| Profile | `GET/PUT/DELETE profile` | `profileApi.js` | `profileApi.js` | |
| Push | `profile/push-token`, `profile/web-push-subscription` | Expo push | `pushApi.js` / `webPush.js` | Ownership-safe register (409 on takeover). |
| Provinces | `GET provinces` | `provincesApi.js` | `provincesApi.js` | |
| Academy | `academy/*`, `certificates/*` | `academyApi.js` | `academyApi.js` | Enrol, attempts, applications, receipt PDF. |
| Constitution TOC | `GET parts`, `GET chapters/{id}` | `constitutionRepository.js` | `constitutionApi.js` + offline | |
| Section | `GET sections/{id}`, search, comments | Section screens | `SectionDetailPage` | |
| Official PDF | `GET constitution/official/amendment3` | `officialConstitutionApi.js` | same | |
| Library | `library/*` | `libraryApi.js` | `libraryApi.js` | |
| Party / organs / presidium | `party/*`, `party-organs*`, `presidium` | matching APIs | matching APIs | |
| Banners / pages | `home-banners`, `pages/{slug}` | matching | matching | |
| Priority projects | `priority-projects`, `GET/{id}`, `like` | `priorityProjectsApi.js` | same | Auth required. |
| Dialogue | `dialogue/*`, blocks, broadcasting auth | `dialogueApi.js` | `dialogueApi.js` + Reverb | |
| Portal notifications | `portal-notifications*` | notifications screens | `notificationsApi.js` | |
| App config | `GET app-config` | used for legal/realtime | `AppConfigContext` | Feature flags. |
| Certificate PDF file | `GET …/certificates/{id}/pdf` | Certificates screen | via academy receipt flow | |

**Consistency:** Primary member features map to documented v1 routes on both Expo and PWA. Health is for monitoring, not required in-app.

---

## 2. Authentication and security relationship

- **API:** Sanctum bearer access token + refresh token flow (`AuthController`, mobile/PWA `authStorage`).
- **Web admin/reader:** Session guard; separate from member tokens.
- **CORS:** `config/cors.php` — production requires `CORS_ALLOWED_ORIGINS`; PWA same-origin `/app` + `/api` avoids CORS in Docker.
- **Rate limits:** Global `throttle:api` on all API routes, plus named limits (auth, assessments, certificates).
- **IDOR / ownership:** [API-SECURITY.md](./API-SECURITY.md) — no MySQL RLS; policies + scoped binding.

---

## 3. Error handling and UX consistency

| Layer | Behaviour |
|-------|-----------|
| **API JSON** | Structured `error` code + `message` (and `errors` for validation). No stack traces in JSON (`App\Exceptions\Handler`). |
| **Mobile / PWA** | `describeApiError` / `catchMessage` + error boundaries. |
| **Web** | Blade error pages for common HTTP codes; users should not see debug traces when `APP_DEBUG=false`. |

**Gap:** Not every screen uses `catchMessage` yet; prefer `error.userMessage` or `catchMessage(e, fallback)` in new catch blocks.

---

## 4. Web vs mobile vs PWA product UX

| Capability | Web (Blade admin/reader) | Mobile (Expo) | PWA (`/app`) |
|------------|--------------------------|---------------|--------------|
| Constitution read | Yes | Yes + offline | Yes + IndexedDB offline |
| Highlights / bookmarks | Limited | Yes | Yes |
| Academy / certificates / dialogue | Admin + some flows | Primary | Primary |
| Installable / push | N/A | Expo push | PWA + Web Push |
| Admin CMS | Web only | N/A | N/A |

---

## 5. Performance (system view)

**Mobile / PWA**

- Offline: versioned caches — see `mobile/docs/OFFLINE-MOBILE.md` and PWA `offline/`.
- Certificate PDF: polling after `202`; align timeouts with backend queue reality.
- Chat: Reverb when available; otherwise poll (PWA thread UI shows Live vs Polling).

**Backend**

- Heavy reads: constitution, library, public JSON — throttling and DB indexes on hot paths.
- CPU: certificate PDF generation should stay **queued**.
- Search: `LIKE` on bodies — see [`SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md`](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md).

**Conceptual load shaping:** [`SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md`](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md).

---

## 6. Recommendations (priority)

1. **Tests:** Keep expanding API feature tests for auth, library access, and dialogue ([`backend-manual/40-operations-testing.md`](./backend-manual/40-operations-testing.md)). IDOR suite: [API-SECURITY.md](./API-SECURITY.md).
2. **Dialogue access model:** Align docs/`is_public`/province with `DialogueChannel::canUserAccess` or implement intended rules.
3. **Mobile / PWA:** Standardize remaining `catch` blocks on `catchMessage` / `userMessage`.
4. **Ops:** Confirm scheduler, queue workers, Reverb, and VAPID keys in production ([`OPS-RUNBOOK.md`](./OPS-RUNBOOK.md), [`PWA.md`](./PWA.md)).
5. **Sentry (optional):** `composer require sentry/sentry-laravel` + `SENTRY_LARAVEL_DSN` when available.

---

## 7. Historical: gap remediation (archived)

The following were tracked in `GAP-REMEDIATION.md` (merged here, file removed).

**Web constitution reader**

- Removed non-functional toolbar actions (highlight, note, translate, TTS); copy points users to mobile for those.
- Search-in-article via in-page find where supported.

**Mobile / PWA**

- Home: visible notice when `GET /home-banners` fails.
- Certificate PDF URLs use `getApiRootUrl()` from `api/client.js`.
- PWA shipped at `/app/` with workflow icons and Web Push (2026-07).

**Tests**

- `ConstitutionOfficialDocumentApiTest` for official amendment PDF endpoint.
- Push ownership, priority project show/like, dialogue report authorization tests (2026-07).

**Docs / ops**

- `PRODUCTION-HARDENING.md`, backend manual updates, `OPS-RUNBOOK` rollback section, logging/Slack ops alerts.
- `PWA.md`, `API-SECURITY.md`.

**Still policy/environment**

- Production TLS, `CORS_ALLOWED_ORIGINS`, broader test backlog, content fixes for null amendment cross-links where needed.

---

*Last updated: 2026-07-10 — PWA + IDOR documentation pass.*
