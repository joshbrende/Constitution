# Environments (WAMP vs Docker)

**Mobile + API:** See [MOBILE-APP.md](./MOBILE-APP.md) and [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md).

This project can be run in two common environments:

- **Local WAMP (Windows)**: Laravel runs directly on your machine, using local MySQL (and optionally local Redis).
- **Docker (Compose)**: Laravel runs inside containers, using container services for MySQL and Redis.

The most important rule:

- **WAMP uses `backend/.env` (local hosts like `127.0.0.1`)**
- **Docker uses `backend/.env.docker` (service hosts like `db`, `redis`)**

---

## Local WAMP (recommended for day-to-day dev)

### Use this env file

- `backend/.env`

### Typical `.env` values

- **DB**:
  - `DB_HOST=127.0.0.1`
  - `DB_DATABASE=zanupf` (or your local DB name)
- **Cache / Queue / Session** (recommended when Redis isn’t available locally):
  - `SESSION_DRIVER=database`
  - `CACHE_STORE=database`
  - `QUEUE_CONNECTION=database`
- **Redis** (only matters if you choose to use Redis locally):
  - `REDIS_HOST=127.0.0.1`

### PHP version

`composer.json` requires **PHP ^8.2**. If WAMP’s default `php` is still 8.1, Composer and Artisan will refuse to run. Switch WAMP to PHP 8.2+, or run tests with `backend/run-tests.ps1`, or use **Docker** (see below). Automated tests on GitHub Actions use PHP 8.4 (workflow: `.github/workflows/backend-tests.yml`).

### Start commands

```bash
cd c:\wamp64\www\constitution\backend
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0 --port=8000
```

### When you change `.env`

```bash
cd c:\wamp64\www\constitution\backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Common WAMP issue: Redis host set to `redis`

If you set `SESSION_DRIVER=redis` / `CACHE_STORE=redis` / `QUEUE_CONNECTION=redis` in WAMP **and** `REDIS_HOST=redis`,
Laravel will throw errors because `redis` is a Docker service name, not a local hostname.

Fix by using database drivers (recommended) or running Redis locally and setting `REDIS_HOST=127.0.0.1`.

---

## Docker (Compose) (recommended for “production-like” testing)

For **free public HTTPS staging** (Cloudflare Tunnel) or a **free 24/7 VM** (Oracle Cloud), see **[STAGING-FREE.md](./STAGING-FREE.md)**.

### Use this env file

- `backend/.env.docker`
- For running Docker, **copy it to** `backend/.env` (containers read `backend/.env`):

```bash
cd c:\wamp64\www\constitution\backend
copy .env.docker .env
```

### Typical `.env` values

- **DB**:
  - `DB_HOST=db`
  - `DB_DATABASE=constitution`
  - `DB_USERNAME=constitution`
  - `DB_PASSWORD=constitution`
- **Cache / Queue / Session**:
  - `SESSION_DRIVER=redis`
  - `CACHE_STORE=redis`
  - `QUEUE_CONNECTION=redis`
  - `REDIS_HOST=redis`

### Start commands

From the project root:

```bash
cd c:\wamp64\www\constitution
docker-compose up --build
```

Then migrate/seed inside the container:

```bash
docker-compose exec app php artisan migrate --seed
```

### URLs / ports

- Nginx (API + web + **PWA**): `http://localhost:8081` — PWA at `/app/`
- MySQL: `127.0.0.1:3308` (forwarded to container `db:3306`)
- Redis: `127.0.0.1:6379` (forwarded)
- Reverb (WebSockets): `REVERB_PORT` default **8090** (phone clients need LAN IP + firewall allow)

### First install (recommended)

With containers running and `backend/.env` configured (`APP_KEY`, `DB_*`):

1. Open `http://localhost:8081/setup`
2. Complete the wizard (system checks → admin → platform → content seed → finish)
3. The wizard creates the database (if needed), runs migrations, seeds platform content, and sets `installed_at`

Manual alternative (developers only):

```bash
docker compose exec app php artisan migrate --seed
```

---

## Mobile API base URL (Expo)

Mobile uses:

- `process.env.EXPO_PUBLIC_API_BASE_URL` (preferred)
- Fallback in code if env not set

Examples:

- WAMP API: `http://<YOUR-LAN-IP>:8000/api/v1`
- Docker API (phone/emulator on LAN): `http://<YOUR-LAN-IP>:8081/api/v1`

---

## PWA (browser / installable)

- Source: `PWA/` → build to `backend/public/app/`
- URL: `http://<YOUR-LAN-IP>:8081/app/`
- API: same-origin `/api/v1` (`VITE_API_BASE_URL`)
- Web Push: set `WEBPUSH_PUBLIC_KEY` / `WEBPUSH_PRIVATE_KEY` (`php artisan webpush:vapid`)
- Live chat: `BROADCAST_CONNECTION=reverb` + healthy `constitution-reverb` container

Full guide: [PWA.md](./PWA.md).

---

## Setup Wizard (one-time)

The backend includes a multi-step **Installation Wizard** at:

- `GET /setup` (public — no login required until setup is complete)

Steps:

1. **Welcome** — introduction and branding.
2. **System checks** — PHP version, extensions, database, storage, migrations (creates DB and runs migrations when you continue).
3. **Administrator** — create the first `system_admin` account (skipped if one already exists).
4. **Platform settings** — installation URL (`APP_URL`), organisation name, support email, legal links, feature toggles (stored in `site_settings`).
5. **Install content** — **required** seed of constitution, banners, academy, library, and static pages.
6. **Complete** — production checklist, `.env` copy block, sets `installed_at`, and locks the wizard.

After installation, authenticated routes redirect to `/setup` until `installed_at` is set. The wizard returns 404 once complete. Progress is restored from the database if the browser session is lost mid-install.

Important notes:

- The wizard **does not create or rewrite** `.env` (except attempting `storage:link` during database setup).
- Production still requires mail, CORS, queue/cron, and mobile API configuration — see the **Production checklist** on the finish step.
- Branding uses public assets `bg-1.jpg` (background) and `Logo.png` (header logo).

### Wizard “Server config checklist”

The wizard displays:

- **Current (detected)**: `APP_NAME`, `APP_URL`, `APP_ENV`, `APP_DEBUG`
- **Recommended for production**: derived from wizard inputs (e.g. `public_site_url`) and best practices

If the current values look like development (e.g. `localhost`, `APP_ENV!=production`, `APP_DEBUG=true`), the wizard shows a warning to the operator.

### Production checklist (finish step)

Before clicking **Complete installation**, review the checklist on step 6. It covers:

| Item | Notes |
|------|--------|
| `.env` / `APP_*` | Copy block from wizard; run `php artisan config:clear` |
| `storage:link` | Attempted during DB setup; verify on checklist |
| Mail (`MAIL_*`) | Required for admin invitations and academy emails |
| `CORS_ALLOWED_ORIGINS` | Browser clients only; see below |
| Queue worker | `php artisan queue:work` (Compose includes `queue` service) |
| Cron / scheduler | `* * * * * php artisan schedule:run` |
| `EXPO_PUBLIC_API_BASE_URL` | Mobile production API base including `/api/v1` |
| Certificate PDFs | PHP `ext-gd` and writable `storage/app` |
| Official PDF | `storage/app/public/constitution-official/amendment3.pdf` |
| Admin invites | Admin → Users → Invite after install |

Progress is restored from the database if the browser session is lost mid-install (`SyncSetupProgress` middleware).

---

## CORS (required for client installs / production)

In production, **CORS must be restricted** to the client’s production domain(s).

Set this in the backend environment:

```env
# Comma-separated list of allowed origins:
# Example:
# CORS_ALLOWED_ORIGINS="https://app.clientdomain.com,https://www.app.clientdomain.com"
CORS_ALLOWED_ORIGINS="https://YOUR-PRODUCTION-DOMAIN"
```

Notes:

- If `APP_ENV=production` and `CORS_ALLOWED_ORIGINS` is not set, the backend will **deny cross-origin requests** by default (safe-by-default).
- In local development (WAMP), if `CORS_ALLOWED_ORIGINS` is not set, CORS falls back to `*` for convenience.

---

## Redirect allowlist (open-redirect protection)

For web login/registration, the app now validates the **intended redirect URL** against an allowlist.

Env variable:

```env
# Comma-separated list of allowed redirect hosts (for absolute URLs).
# If empty, the current host is used as the only allowed host.
REDIRECT_ALLOWED_HOSTS="app.clientdomain.com,www.app.clientdomain.com"
```

Rules:

- **Relative paths** (e.g. `/dashboard`) are always allowed.
- **Protocol-relative** (`//evil.com`) or external hosts not in `REDIRECT_ALLOWED_HOSTS` are rejected and the user is redirected to `/dashboard` instead.

---

## Certificate expiry policy (new)

Certificate validity now supports an optional default expiry window for newly issued certificates.

Env variable:

```env
# Default: 730 days (2 years)
# Set 0 (or negative) to disable automatic expiry assignment
CERTIFICATE_DEFAULT_EXPIRY_DAYS=730
```

Notes:

- New certificates issued by the membership flow get `expires_at = issued_at + CERTIFICATE_DEFAULT_EXPIRY_DAYS`.
- Certificate verification status is now operational:
  - `active`
  - `expired`
  - `revoked`

## Production / ops requirements

For production, ensure:

- **Scheduler** runs (cron or `php artisan schedule:work`): `ops:queue-health` (every 5 min), `ops:cleanup-security-data` (daily).
- **Queue worker** runs (`php artisan queue:work`) for certificate PDF generation and other jobs.
- See [OPS-RUNBOOK.md](OPS-RUNBOOK.md) for env vars (`AUDIT_LOG_RETENTION_DAYS`, `QUEUE_HEALTH_MAX_FAILED_JOBS`, etc.) and commands.

---

## Quick “Which one am I in?” checklist

- If the backend is running via `php artisan serve` → **WAMP** → use **`DB_HOST=127.0.0.1`**
- If the backend is running via `docker-compose up` → **Docker** → use **`DB_HOST=db`** and **`REDIS_HOST=redis`**

