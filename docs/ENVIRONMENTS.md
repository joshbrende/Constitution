# Environments (WAMP vs Docker)

**Mobile + API:** See [`BACKEND-MOBILE-CONSISTENCY.md`](./BACKEND-MOBILE-CONSISTENCY.md) and [`mobile/docs/OFFLINE-MOBILE.md`](../mobile/docs/OFFLINE-MOBILE.md).

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

- Nginx: `http://localhost:8080`
- MySQL: `127.0.0.1:3307` (forwarded to container `db:3306`)
- Redis: `127.0.0.1:6379` (forwarded)

---

## Mobile API base URL (Expo)

Mobile uses:

- `process.env.EXPO_PUBLIC_API_BASE_URL` (preferred)
- Fallback in code if env not set

Examples:

- WAMP API: `http://<YOUR-LAN-IP>:8000/api/v1`
- Docker API (if accessing from phone/emulator): `http://<YOUR-LAN-IP>:8080/api/v1`

---

## Setup Wizard (one-time)

The backend includes a one-time **Setup Wizard** at:

- `GET /setup` (requires login + role `system_admin`)

Purpose:

- Stores **platform defaults** (organisation name, support email, public site URL, legal links, feature toggles) in the **database** (`site_settings` table).
- Avoids editing `.env` from the web UI (safer and more hosting-compatible).

Important note:

- The wizard **does not create or rewrite** `.env`.
- Production still requires correct server environment values (via hosting env vars / secrets / `.env`).

### Wizard “Server config checklist”

The wizard displays:

- **Current (detected)**: `APP_NAME`, `APP_URL`, `APP_ENV`, `APP_DEBUG`
- **Recommended for production**: derived from wizard inputs (e.g. `public_site_url`) and best practices

If the current values look like development (e.g. `localhost`, `APP_ENV!=production`, `APP_DEBUG=true`), the wizard shows a warning to the operator.

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

