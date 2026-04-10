## Docker deployment guide

This document describes how to run the Constitution backend with Docker and `docker-compose`. It is the reference for future Docker/Kubernetes work.

### 1. Overview

- **Backend app**: Laravel 12 (PHP 8.2, `backend/`).
- **Web server**: Nginx serving `backend/public`.
- **Database**: MySQL 8 (service name `db`).
- **Cache/queue**: Redis 7 (service name `redis`).

The main files are:

- `backend/Dockerfile` – PHP‑FPM image for Laravel.
- `nginx.conf` – Nginx vhost pointing to `public/index.php`.
- `docker-compose.yml` – wires `app`, `nginx`, `db`, `redis` together.

### 2. One‑time setup

1. Ensure Docker and `docker-compose` (or Docker Desktop) are installed.
2. **Compose secrets (required):** In the **repository root** (same folder as `docker-compose.yml`), copy `compose.env.example` to `.env`. Docker Compose reads this file for `${DB_PASSWORD}`, `${MYSQL_PASSWORD}`, and `${MYSQL_ROOT_PASSWORD}` in `docker-compose.yml`. Adjust values for production; the example matches local dev defaults documented below.
3. From the project root (`c:\wamp64\www\constitution`), use the Docker-specific env for Laravel:

```bash
cd backend
copy .env.docker .env   # or cp on Linux/macOS
```

4. Verify `backend/.env` for Docker matches the database credentials (especially `DB_PASSWORD` must match the root `.env` value used by Compose for the app container).

- Set:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=constitution
DB_USERNAME=constitution
DB_PASSWORD=constitution

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379
```

- Recommended for Docker: set `SESSION_DRIVER=redis`, `CACHE_STORE=redis`, and `QUEUE_CONNECTION=redis`.

5. **Optional — seeded admin user:** If you run `php artisan db:seed`, set `ADMIN_SEED_PASSWORD` in `backend/.env` (see `backend/.env.example`). Never commit real passwords.

6. Generate the app key (first run only):

```bash
docker-compose run --rm app php artisan key:generate
```

### 3. Running the stack locally

From the project root:

```bash
docker-compose up --build
```

- Nginx: `http://localhost:8080`
- MySQL: host `127.0.0.1`, port `3307`, db `constitution`, user `constitution`, password `constitution`.
- Redis: `127.0.0.1:6379` (forwarded).

To stop:

```bash
docker-compose down
```

### 4. Database migrations and seeders

After the containers are up (first time on a new database):

```bash
docker-compose exec app php artisan migrate --seed
```

To seed the default admin user (`admin@zanupf.org`), set `ADMIN_SEED_PASSWORD` in `backend/.env` before `--seed`. If it is unset, `AdminUserSeeder` skips that user (other seeders still run).

You can re‑run migrations or seeders the same way whenever needed.

### 5. Queue worker (optional for now)

The current `docker-compose.yml` runs only the web `app` service. When we are ready to add a dedicated queue worker:

```yaml
  queue:
    build:
      context: ./backend
      dockerfile: Dockerfile
    command: php artisan queue:work --tries=3 --timeout=90
    depends_on:
      - app
      - redis
    env_file:
      - ./backend/.env
```

Then:

```bash
docker-compose up -d queue
```

### 6. Production notes (for later)

- Use a separate `.env` for production secrets (do not commit).
- Put Nginx behind TLS (e.g. reverse proxy or cloud load balancer).
- Move the database to a managed service if you adopt Kubernetes later; reuse the same Docker images.

---

## Local dev vs Docker env (important)

- **Local WAMP** uses `backend/.env` with DB host `127.0.0.1` and typically `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`.
- **Docker** uses `backend/.env.docker` (copied to `.env` for containers) with DB host `db` and Redis host `redis`.

This file should be updated as we refine the Docker/Kubernetes strategy.

