# Production hardening checklist

Use this before exposing the backend or publishing mobile builds to end users.

## Laravel (`backend/.env`)

| Item | Requirement |
|------|-------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Unique, never committed or reused from development |
| `APP_URL` | Public HTTPS URL of the application (used when generating some absolute links) |
| Database credentials | Strong passwords; restricted network access |
| `CORS_ALLOWED_ORIGINS` | Comma-separated **exact** origins for browser clients (e.g. Expo web, admin site). In `production`, an **empty** value means **no** cross-origin browser access to the API (see `config/cors.php`). Native mobile apps use Bearer tokens and are not limited by CORS the same way. |

**Setup Wizard reminder:** `/setup` (system_admin only) stores platform defaults in DB. It **does not** write `.env` — the operator must still set the variables above in production.

## Storage and official PDF

| Item | Requirement |
|------|-------------|
| `php artisan storage:link` | Run once per server so `/storage/...` URLs work |
| Official amendment PDF | `storage/app/public/constitution-official/amendment3.pdf` — public by design; do not upload restricted documents here |

## HTTPS

| Item | Requirement |
|------|-------------|
| API and downloads | Terminate TLS at reverse proxy or load balancer; mobile production builds should use **`https://`** API base URLs |
| iOS | Plain HTTP to LAN IPs is for development only; production typically needs HTTPS (and ATS configuration if needed) |

## Mobile (`EXPO_PUBLIC_API_BASE_URL`)

| Item | Requirement |
|------|-------------|
| Value | Full base including `/api/v1`, e.g. `https://api.example.com/api/v1` |
| Per environment | Use EAS secrets or env-specific `.env` so production never points at a developer machine |

## Operational

| Item | Requirement |
|------|-------------|
| Backups | Database and `storage/app/public` (uploads, official PDF) |
| Audit logs | Retention policy (`operations:cleanup-security-data` / `config/operations.php`) |
| Tests | `php artisan test` in CI (PHP 8.2+); see `tests/Feature/ConstitutionOfficialDocumentApiTest.php` |

## Store submission (Google Play + Apple App Store)

Use this section when preparing your first production release of the **mobile app**.

### Reviewer access (strict)

- **Backend must be live** and reachable over **HTTPS** during review.
- Provide either:
  - **Demo mode** with full feature access, or
  - **Demo account credentials** (recommended) in the store “review notes”.
- Ensure reviewers can reach:
  - Academy course list + enrol + assessment flow
  - Dialogue (open UGC) + report/block
  - Certificates screen (if present)
  - Legal pages (Terms/Privacy/Cookies)

### UGC (Dialogue/chat) compliance

Because Dialogue is open to all users, you must maintain:
- **Reporting**: report message + report thread (in-app).
- **Blocking**: ability to block abusive users.
- **Moderation**: admin reports queue with timely responses and takedown actions (remove message / lock thread).
- **Published contact info**: support contact must be visible to users (store listing + in-app Help/Terms).

Apple references UGC requirements under “User-Generated Content” (Guideline 1.2). See: `https://developer.apple.com/app-store/review/guidelines/`.

### Privacy + legal (store listing)

- Store listing must include a working **Privacy Policy URL**.
- Ensure Terms/Privacy/Cookies are available in-app and/or via public web URLs.
- Ensure your privacy policy discloses (at minimum):
  - Identity/contact data (name, surname, email)
  - National ID (if collected/required)
  - Learning activity (enrolments, attempts, scores, certificates)
  - UGC content (messages + attachments)
  - Audit/security logging (e.g. IP/user agent for admin actions)

### Google Play Console requirements (non-code)

From Google Play Console requirements, ensure:
- Developer account verification is complete (org vs personal).
- App metadata is complete and accurate.
- **Data safety** form is completed accurately.
- Provide demo access details for review.

Reference: `https://support.google.com/googleplay/android-developer/answer/10788890?hl=en`.

### Apple App Store Connect requirements (non-code)

- Complete App Store Connect “App Privacy” questionnaire accurately.
- If you use tracking SDKs, implement ATT prompt. (If you do not, ensure your declarations match.)
- Ensure the app is stable (no crashes, broken login, dead links).

---

*See also: [`BACKEND-MOBILE-CONSISTENCY.md`](./BACKEND-MOBILE-CONSISTENCY.md), [`ENVIRONMENTS.md`](./ENVIRONMENTS.md), [`OPS-RUNBOOK.md`](./OPS-RUNBOOK.md).*
