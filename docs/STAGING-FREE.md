# Free staging & production-like testing

How to exercise the Constitution backend (Laravel + MySQL + Redis + queue worker) in a **production-like** way at **$0** — without Vercel or other platforms that do not support this stack.

**Related:** [ENVIRONMENTS.md](./ENVIRONMENTS.md) · [DOCKER.md](../DOCKER.md) · [PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md) · [OPS-RUNBOOK.md](./OPS-RUNBOOK.md)

---

## Current team decision (local Docker)

**Use this until you need public HTTPS or mobile-data testing.**

| Item | Choice |
|------|--------|
| Runtime | `docker compose up` (or `docker-compose.prod.yml` for read-only code mounts) |
| Web + API | `http://localhost:8081` |
| Mobile (same Wi‑Fi) | `http://<LAN-IP>:8081/api/v1` in `mobile/.env` |
| Public / mobile data | Defer — add Cloudflare quick tunnel or cloud VM later |

**Daily commands** (repository root):

```powershell
docker compose up -d
docker compose ps
```

Health check: `http://localhost:8081/api/v1/health` → `{"status":"ok",...}`

---

## What you are testing

| Layer | Technology |
|-------|------------|
| Web + API | Laravel behind nginx (`docker-compose.prod.yml`) |
| Database | MySQL 8 |
| Cache / sessions / queues | Redis |
| Background jobs | `queue` service (`php artisan queue:work`) |
| Mobile | Expo app → `EXPO_PUBLIC_API_BASE_URL` |

**Do not use Vercel** for this backend — it does not run long-lived PHP, MySQL, Redis, or queue workers.

---

## Choose an option

| Option | Cost | HTTPS | Mobile over internet | Always on | Best for |
|--------|------|-------|----------------------|-----------|----------|
| **A. Local prod Compose** | $0 | No | LAN only | While PC runs | Admin flows, API, Scribe |
| **B. Cloudflare quick tunnel** | $0 | Yes | Yes | While PC + tunnel run | Fast external / mobile test |
| **C. Cloudflare named tunnel** | $0* | Yes | Yes | While PC + tunnel run | Stable URL (*domain on Cloudflare) |
| **D. Oracle Cloud free VM** | $0 | Yes | Yes | Yes | 24/7 “real” staging server |

---

## Prerequisites (all options)

1. **Docker Desktop** running on your machine (or Docker on a Linux VM).
2. **Repository root secrets** — copy `compose.env.example` → `.env` at the repo root; set strong `DB_PASSWORD`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`.
3. **Laravel env** — copy `backend/.env.docker` → `backend/.env` (or copy `backend/.env.example` and set Docker hosts below).
4. **Generate app key** (once):

```powershell
cd c:\wamp64\www\constitution
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate
```

### Minimum `backend/.env` for Docker staging

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8081

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=constitution
DB_USERNAME=constitution
DB_PASSWORD=<same as root .env DB_PASSWORD>

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

SESSION_ENCRYPT=true
LOG_LEVEL=info
MAIL_MAILER=log

# Lock /setup before going public (see Security section)
SETUP_ACCESS_TOKEN=<long-random-secret>
```

Match `DB_PASSWORD` with the value in the repository-root `.env` used by Compose.

---

## Option A — Local production-like Compose (free, no public URL)

From the repository root:

```powershell
docker compose -f docker-compose.prod.yml up -d --build
```

| Endpoint | URL |
|----------|-----|
| Web + API | `http://localhost:8081` |
| Health | `http://localhost:8081/api/v1/health` |
| Setup wizard | `http://localhost:8081/setup` |

### First install

1. Open `http://localhost:8081/setup`.
2. Complete all wizard steps (checks → admin → platform → content → finish).
3. Confirm `GET /api/v1/health` returns `{"status":"ok",...}`.

Manual alternative (developers only):

```powershell
docker compose -f docker-compose.prod.yml exec app php artisan migrate --seed
```

### Mobile on the same Wi‑Fi

```env
# mobile/.env
EXPO_PUBLIC_API_BASE_URL=http://<YOUR-LAN-IP>:8081/api/v1
```

Find your LAN IP: `ipconfig` (Windows) → IPv4 address. Phone must be on the same network. **HTTP to a LAN IP is development-only** (not App Store–style production).

### Stop

```powershell
docker compose -f docker-compose.prod.yml down
```

---

## Option B — Cloudflare quick tunnel (free HTTPS, fastest public test)

Exposes `localhost:8081` to the internet with HTTPS. No VPS and no domain required.

### 1. Start prod Compose (Option A)

```powershell
docker compose -f docker-compose.prod.yml up -d --build
```

### 2. Install `cloudflared`

Download and install for Windows:

[https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/](https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/)

### 3. Start a quick tunnel

```powershell
cloudflared tunnel --url http://localhost:8081
```

Note the URL printed, e.g. `https://random-words.trycloudflare.com`.

**Limitation:** Quick tunnel URLs change each time you restart `cloudflared`. For a stable hostname, use Option C.

### 4. Update Laravel for the public URL

Edit `backend/.env`:

```env
APP_URL=https://random-words.trycloudflare.com
CORS_ALLOWED_ORIGINS=https://random-words.trycloudflare.com
```

Apply and restart:

```powershell
docker compose -f docker-compose.prod.yml exec app php artisan config:clear
docker compose -f docker-compose.prod.yml restart app nginx queue
```

If the wizard is not finished yet, open:

`https://random-words.trycloudflare.com/setup?setup_token=<SETUP_ACCESS_TOKEN>`

### 5. Mobile

```env
# mobile/.env
EXPO_PUBLIC_API_BASE_URL=https://random-words.trycloudflare.com/api/v1
```

Restart Expo (`npx expo start`). Test on **mobile data** (not only Wi‑Fi) to confirm the tunnel works.

### 6. Verify

```text
GET https://random-words.trycloudflare.com/api/v1/health
→ 200 {"status":"ok","checks":{"database":true,"redis":true}}
```

---

## Option C — Cloudflare named tunnel (stable free URL)

Use when you have a **domain on Cloudflare** (free plan is fine) and want a fixed hostname like `staging.example.org`.

1. Log in: `cloudflared tunnel login`
2. Create tunnel: `cloudflared tunnel create constitution-staging`
3. Configure DNS in the Cloudflare dashboard (CNAME to the tunnel).
4. Point the tunnel at `http://localhost:8081` in the tunnel config.

Full guide: [Cloudflare Tunnel documentation](https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/).

Set `APP_URL` and `CORS_ALLOWED_ORIGINS` to your stable hostname, then `config:clear` and restart containers as in Option B.

---

## Option D — Oracle Cloud Always Free VM (free 24/7 server)

Best when you need staging **always on** without keeping your PC running.

### Overview

1. Create an [Oracle Cloud Free Tier](https://www.oracle.com/cloud/free/) account.
2. Launch an **Ampere A1** Ubuntu 22.04 instance (2–4 GB RAM recommended).
3. Open inbound ports **22**, **80**, **443** in the security list / firewall.
4. SSH in and install Docker + Docker Compose.
5. Clone this repository, configure `.env` files, run prod Compose.
6. Put **Caddy** or **nginx + Let’s Encrypt** in front for HTTPS (or use Cloudflare proxy in front of the VM).

### On the VM (Ubuntu)

```bash
# Install Docker (official convenience script or distro packages)
sudo apt update && sudo apt install -y docker.io docker-compose-plugin git

git clone <your-repo-url> constitution
cd constitution

cp compose.env.example .env
# Edit .env — strong passwords

cp backend/.env.docker backend/.env
# Edit backend/.env — APP_URL=https://staging.yourdomain.org, mail, SETUP_ACCESS_TOKEN, etc.

docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate
docker compose -f docker-compose.prod.yml up -d --build
```

Complete the wizard at `https://staging.yourdomain.org/setup` (after TLS is configured).

### Scheduler on the VM

Cron on the host (every minute):

```cron
* * * * * cd /path/to/constitution && docker compose -f docker-compose.prod.yml exec -T app php artisan schedule:run >> /dev/null 2>&1
```

The `queue` service in Compose handles `queue:work`; see [OPS-RUNBOOK.md](./OPS-RUNBOOK.md).

### HTTPS with Caddy (minimal example)

Install Caddy, then a `Caddyfile`:

```text
staging.yourdomain.org {
    reverse_proxy localhost:8081
}
```

Map Compose nginx to host port 8081 only (default in `docker-compose.prod.yml`).

---

## Mobile builds (staging)

| Item | Value |
|------|--------|
| Env var | `EXPO_PUBLIC_API_BASE_URL` |
| Format | Full base including `/api/v1`, e.g. `https://staging.example.org/api/v1` |
| Protocol | **HTTPS** required for realistic production testing |
| Secrets | Use EAS secrets or `mobile/.env` (gitignored) — never commit staging URLs with tokens |

See [PRODUCTION-HARDENING.md § Mobile](./PRODUCTION-HARDENING.md#mobile-expo_public_api_base_url).

---

## Security checklist (public staging)

Even for free staging, treat a public URL as **exposed**:

| Item | Action |
|------|--------|
| `APP_DEBUG` | `false` |
| `APP_ENV` | `production` |
| `SETUP_ACCESS_TOKEN` | Set before exposing `/setup`; wizard locks after `installed_at` |
| Passwords | Strong values in root `.env` and `backend/.env` |
| Test data | No real national IDs or member PII |
| API docs | Regenerate on deploy; restrict `/docs/` (`bash backend/scripts/setup-docs-auth.sh <user>`) |
| Mail | Use [Mailtrap](https://mailtrap.io) free tier or `MAIL_MAILER=log` if email is not under test |

See [PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md) for the full checklist.

---

## Operations quick reference

```powershell
# Logs
docker compose -f docker-compose.prod.yml logs -f app queue

# Queue restart after deploy
docker compose -f docker-compose.prod.yml exec app php artisan queue:restart

# Tests (from dev machine or CI)
docker compose exec app php artisan test

# API docs regenerate
docker compose -f docker-compose.prod.yml exec app bash scripts/generate-api-docs.sh

# Health
curl http://localhost:8081/api/v1/health
```

---

## Platforms that are a poor fit (free tier)

| Platform | Why not for this project |
|----------|---------------------------|
| **Vercel** | No Laravel + MySQL + Redis + queue worker |
| **Render (free)** | Services sleep; no simple PHP + MySQL + worker bundle |
| **Railway (free credit)** | Credit runs out quickly with DB + Redis + app |
| **Neon / Supabase** | PostgreSQL only — project uses MySQL |

---

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Redirect to `/setup` on admin | Finish wizard or set `installed_at` in `site_settings` |
| Mobile cannot reach API | Use HTTPS tunnel URL; check `EXPO_PUBLIC_API_BASE_URL` ends with `/api/v1` |
| Health `503` / `redis: false` | Ensure `queue` and `redis` containers are up; rebuild app image if `phpredis` missing |
| CORS errors in browser | Set `CORS_ALLOWED_ORIGINS` to exact tunnel/origin URL |
| Scribe Try It Out fails | `APP_URL` and `CORS_ALLOWED_ORIGINS` must match how you open the docs |
| Tunnel URL changed | Update `APP_URL`, mobile env, `config:clear`, restart — or use Option C |

---

## Next step after free staging

When moving beyond testing:

- Party / government infrastructure per [INTEGRATIONS.md](./INTEGRATIONS.md)
- TLS at load balancer — [LOAD-BALANCER.md](./LOAD-BALANCER.md)
- Full ops — [OPS-RUNBOOK.md](./OPS-RUNBOOK.md)

---

*Last updated: documentation pass for free staging (Cloudflare Tunnel + Oracle Cloud).*
