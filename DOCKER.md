## Docker deployment guide

Run the Constitution **backend** with Docker Compose. See also [`docs/ENVIRONMENTS.md`](docs/ENVIRONMENTS.md) and [`docs/OPS-RUNBOOK.md`](docs/OPS-RUNBOOK.md).

### 1. Overview

| Service | Role |
|---------|------|
| `app` | Laravel PHP-FPM |
| `nginx` | Web server → `backend/public` |
| `db` | MySQL 8 |
| `redis` | Cache, sessions, queues |
| `queue` | `php artisan queue:work redis --queue=default,mail` |

Files: `backend/Dockerfile`, `nginx.conf`, `docker-compose.yml`.

### 2. One-time setup

1. Install Docker Desktop (or Docker + Compose).
2. **Compose secrets:** Copy `compose.env.example` → `.env` at the **repository root** (`DB_PASSWORD`, `MYSQL_*`).
3. **Laravel env:** Copy `backend/.env.docker` → `backend/.env` and set `APP_KEY`:

```bash
cd backend
copy .env.docker .env
docker compose run --rm app php artisan key:generate
```

4. Ensure `DB_PASSWORD` in `backend/.env` matches the root `.env` value used by Compose.

Recommended Docker drivers in `backend/.env`:

```env
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
DB_HOST=db
```

### 3. Running locally

From the repository root:

```bash
docker compose up -d --build
```

| Endpoint | URL |
|----------|-----|
| Web + API | `http://localhost:8081` |
| API example | `http://localhost:8081/api/v1/home-banners` |
| MySQL (host) | `127.0.0.1:3308` |
| Redis (host) | `127.0.0.1:6379` |

Stop: `docker compose down`

### 4. First install

**Recommended — Setup Wizard**

1. Open `http://localhost:8081/setup`
2. Complete all six steps (checks → admin → platform → content → finish)
3. Wizard creates DB (if permitted), migrates, seeds content, sets `installed_at`

**Manual (developers)**

```bash
docker compose exec app php artisan migrate --seed
```

Optional seeded admin: set `ADMIN_SEED_PASSWORD` in `backend/.env` before seeding.

### 5. Queue worker

Compose starts the `queue` service automatically. After code deploy:

```bash
docker compose exec app php artisan queue:restart
```

See [`docs/OPS-RUNBOOK.md`](docs/OPS-RUNBOOK.md) § Queue worker.

### 6. API documentation (Scribe)

Regenerate after changing `routes/api.php` or API controller annotations:

```bash
docker compose exec app bash scripts/generate-api-docs.sh
```

Or: `docker compose exec app composer docs:api`

| Output | URL |
|--------|-----|
| HTML | `http://localhost:8081/docs/index.html` |
| OpenAPI | `http://localhost:8081/docs/openapi.yaml` |
| Postman | `http://localhost:8081/docs/collection.json` |

Optional: set `SCRIBE_AUTH_KEY` in `backend/.env` to a valid bearer token before generating — Scribe will call authenticated GET endpoints for live response samples.

Generated files live in `backend/public/docs/` (gitignored; regenerate on each deploy).

### 7. Production notes

- Use strong secrets in root `.env` and `backend/.env`; never commit them.
- Terminate TLS at a reverse proxy; set `APP_URL` to HTTPS.
- Complete the wizard **Production checklist** (mail, CORS, cron, mobile API URL).
- Back up MySQL volume `db_data` and `storage/app/public`.
- Regenerate API docs: `composer docs:api` (or `bash scripts/generate-api-docs.sh` in the container).
- Restrict `/docs/` in production — run `bash backend/scripts/setup-docs-auth.sh <username>` (enables nginx basic auth via `nginx.docs-auth.conf`), or omit `public/docs/` from the production artifact.

---

**Local WAMP** uses `DB_HOST=127.0.0.1` and typically database-backed cache/session/queue — see [`docs/ENVIRONMENTS.md`](docs/ENVIRONMENTS.md).
