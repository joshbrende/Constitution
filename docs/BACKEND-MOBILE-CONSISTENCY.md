# Backend ↔ mobile: consistency, UX, performance

Step-by-step review of **`backend/`** (Laravel API + web) and **`mobile/`** (Expo). Use with [`backend-manual/20-api-overview.md`](./backend-manual/20-api-overview.md), [`ENVIRONMENTS.md`](./ENVIRONMENTS.md), and [`mobile/docs/OFFLINE-MOBILE.md`](../mobile/docs/OFFLINE-MOBILE.md).

---

## 1. API surface alignment

Mobile uses `axios` with `baseURL` = `{origin}/api/v1` (see `EXPO_PUBLIC_API_BASE_URL`). Relative paths below are under `/api/v1`.

| Area | Backend route(s) | Mobile module | Notes |
|------|------------------|---------------|--------|
| Auth | `POST auth/register`, `login`, `refresh`, `forgot-password`; `POST auth/logout` (Sanctum) | `LoginScreen`, `RegisterScreen`, `ForgotPasswordScreen`, `client.js` | Refresh + 401 handling centralized in interceptor. |
| Profile | `GET/PUT profile` | `profileApi.js` | |
| Provinces | `GET provinces` | `provincesApi.js` | |
| Academy | `academy/*`, `certificates/*` (in `academyApi.js`) | `academyApi.js` | Includes enrol, attempts, submit, list/generate certs. |
| Constitution TOC | `GET parts`, `GET chapters/{id}` | `constitutionRepository.js`, `loadPartsForToc.js` | `doc` query matches backend (`zanupf`, `zimbabwe`, `amendment3`). |
| Section | `GET sections/{id}`, search, comments | `sectionCache.js`, `ConstitutionListScreen`, `SectionDetailScreen` | Search is online-only; UX hint when offline. |
| Official PDF | `GET constitution/official/amendment3` | `officialConstitutionApi.js` | |
| Library | `library/categories`, `documents`, `documents/{id}` | `libraryApi.js` | |
| Party / organs / presidium | `party/profile`, `party-organs*`, `presidium` | `partyApi.js`, `partyOrgansApi.js`, `presidiumApi.js` | |
| Banners / pages | `home-banners`, `pages/{slug}` | `homeBannersApi.js`, `staticPagesApi.js` | Banner failures show a notice on Home (not silent empty). |
| Priority projects | `priority-projects`, `like` | `priorityProjectsApi.js` | |
| Dialogue | `dialogue/*` | `dialogueApi.js` | |
| Certificate PDF file | `GET …/certificates/{id}/pdf` (full URL) | `CertificatesScreen` + `getApiRootUrl()` | Must match configured API host. |

**Consistency:** All primary mobile features map to documented v1 routes. The app does **not** call `GET /api/v1/health` from the client; use external monitoring for uptime.

---

## 2. Authentication and security relationship

- **API:** Sanctum bearer access token + refresh token flow (`AuthController`, mobile `authStorage`).
- **Web admin/reader:** Session guard; separate from mobile tokens.
- **CORS:** `config/cors.php` — production requires `CORS_ALLOWED_ORIGINS`; mobile native requests are not browser CORS, but web clients are.
- **Rate limits:** Global `throttle:api` on all API routes, plus named limits (auth forgot/refresh, assessments, certificates, certificate-verify web).

---

## 3. Error handling and UX consistency

| Layer | Behaviour |
|-------|-----------|
| **API JSON** | Structured `error` code + `message` (and `errors` for validation). No stack traces in JSON (`App\Exceptions\Handler`). |
| **Mobile** | `describeApiError` / `catchMessage` + `ErrorFallbackScreen` / `Problem` route + `AppErrorBoundary` for JS crashes. |
| **Web** | Blade error pages for common HTTP codes; users should not see debug traces when `APP_DEBUG=false`. |

**Gap:** Not every screen uses `catchMessage` yet; prefer `error.userMessage` or `catchMessage(e, fallback)` in new catch blocks.

---

## 4. Web vs mobile product UX

| Capability | Web (Blade reader) | Mobile |
|------------|----------------------|--------|
| Constitution read | Yes | Yes + offline cache |
| Search (full section search) | Via API-backed flows where implemented | Constitution list search online-only |
| Highlights / notes / TTS | Not on web (by design) | Planned / product differentiation |
| Academy / certificates / dialogue | Mixed (some web dashboard) | Primary member UX |
| Admin CMS | Web only | N/A |

This split is **intentional**; avoid duplicating complex reader features on web unless product asks.

---

## 5. Performance (system view)

**Mobile**

- Offline: versioned AsyncStorage, stale-while-revalidate, section LRU — see `mobile/docs/OFFLINE-MOBILE.md`.
- Certificate PDF: polling after `202`; align timeouts with backend queue reality.

**Backend**

- Heavy reads: constitution, library, public JSON — mitigated by throttling and DB indexes on hot paths (chapters by `constitution_slug`, sections by chapter, `section_versions`, dialogue messages, etc.).
- CPU: certificate PDF generation should stay **queued**; ensure workers run in production.
- Search: `LIKE` on bodies does not scale infinitely; acceptable for moderate corpora — see [`SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md`](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md).

**Conceptual load shaping:** [`SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md`](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md) (demand vs supply, queues, transparency).

---

## 6. Recommendations (priority)

1. **Tests:** Expand API feature tests for auth, library access rules, and dialogue — coverage is thin vs surface area ([`backend-manual/40-operations-testing.md`](./backend-manual/40-operations-testing.md)).
2. **Policies:** Consider Laravel Policies for complex API authorization (today often controller-local).
3. **Mobile:** Standardize remaining `catch` blocks on `catchMessage` / `userMessage`.
4. **Ops:** Confirm scheduler, queue workers, and Slack/ops webhooks in production ([`OPS-RUNBOOK.md`](./OPS-RUNBOOK.md)).
5. **Sentry (optional):** `composer require sentry/sentry-laravel` + `SENTRY_LARAVEL_DSN` when OpenSSL/Composer available in deploy environment.

---

## 7. Historical: gap remediation (archived)

The following were tracked in `GAP-REMEDIATION.md` (merged here, file removed).

**Web constitution reader**

- Removed non-functional toolbar actions (highlight, note, translate, TTS); copy points users to mobile for those.
- Search-in-article via in-page find where supported.

**Mobile**

- Home: visible notice when `GET /home-banners` fails.
- Certificate PDF URLs use `getApiRootUrl()` from `api/client.js`.

**Tests**

- `ConstitutionOfficialDocumentApiTest` for official amendment PDF endpoint.

**Docs / ops**

- `PRODUCTION-HARDENING.md`, backend manual updates, `OPS-RUNBOOK` rollback section, logging/Slack ops alerts.

**Still policy/environment**

- Production TLS, `CORS_ALLOWED_ORIGINS`, broader test backlog, content fixes for null amendment cross-links where needed.

---

*Last updated: cross-stack documentation pass.*
