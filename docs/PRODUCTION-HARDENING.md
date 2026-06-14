# Production hardening checklist

Use this before exposing the backend or publishing mobile builds to end users.

## Setup Wizard (first run)

| Item | Detail |
|------|--------|
| URL | `GET /setup` — **public** until `installed_at` is set in `site_settings` |
| Steps | Welcome → system checks → administrator → platform URL/settings → **required** content seed → finish |
| Database | Wizard stores org name, support email, legal links, toggles in **`site_settings`** |
| `.env` | Wizard **does not write** `.env`; finish step shows copy blocks + **Production checklist** |
| Lock | After complete, `/setup/*` returns 404; dashboard requires `installed_at` |

See [ENVIRONMENTS.md § Setup Wizard](./ENVIRONMENTS.md#setup-wizard-one-time).

## Laravel (`backend/.env`)

| Item | Requirement |
|------|-------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Unique; `php artisan key:generate` if missing (wizard checks this) |
| `APP_URL` | Public HTTPS URL — match **Installation URL** from wizard step 4 |
| Database credentials | Strong passwords; restricted network access |
| `MAIL_*` | Real SMTP (not `log`) for invitations and academy notifications |
| `QUEUE_CONNECTION` | `redis` or `database` with a running worker (see [OPS-RUNBOOK.md](./OPS-RUNBOOK.md)) |
| `CORS_ALLOWED_ORIGINS` | Comma-separated **exact** origins for browser clients. In `production`, empty = no cross-origin browser API access (`config/cors.php`). Native mobile uses Bearer tokens. |

## Storage and official PDF

| Item | Requirement |
|------|-------------|
| `php artisan storage:link` | Run once per server so `/storage/...` URLs work (wizard attempts during DB setup) |
| Official amendment PDF | `storage/app/public/constitution-official/amendment3.pdf` — public by design |
| Certificate PDFs | PHP **ext-gd** enabled; `storage/app` writable (TCPDF font cache) |

## HTTPS

| Item | Requirement |
|------|-------------|
| API and downloads | Terminate TLS at reverse proxy or load balancer |
| Mobile production builds | **`https://`** API base URLs only |
| iOS | Plain HTTP to LAN IPs is development-only |

## Mobile (`EXPO_PUBLIC_API_BASE_URL`)

| Item | Requirement |
|------|-------------|
| Value | Full base including `/api/v1`, e.g. `https://api.example.com/api/v1` |
| Per environment | EAS secrets or env-specific `.env` — never point production at a dev machine |

## Operational

| Item | Requirement |
|------|-------------|
| Scheduler | Cron: `php artisan schedule:run` every minute |
| Queue worker | `php artisan queue:work` — Docker Compose includes a `queue` service |
| Backups | Database and `storage/app/public` |
| Audit logs | Retention via `ops:cleanup-security-data` / `config/operations.php` |
| Tests | `php artisan test` in CI (PHP 8.2+) |
| Post-install admins | Invite team via **Admin → Users → Invite backend user** |

## Store submission (Google Play + Apple App Store)

### Reviewer access

- Backend live over **HTTPS** during review
- Demo account or demo mode in store review notes
- Reviewers can reach: Academy flow, Dialogue + report/block, Certificates, legal pages

### UGC (Dialogue)

- Reporting, blocking, moderation queue, published support contact
- Apple Guideline 1.2 (User-Generated Content)

### Privacy + legal

- Store listing Privacy Policy URL matches in-app / wizard legal links
- Policy covers: identity, National ID (if required), learning activity, UGC, audit logging

### Google Play / Apple

- Complete Data safety / App Privacy questionnaires accurately
- Stable build (login, links, no crashes)

---

*See also: [ENVIRONMENTS.md](./ENVIRONMENTS.md), [OPS-RUNBOOK.md](./OPS-RUNBOOK.md), [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md).*
