# Load Balancer (for future production deployment)

## Why a load balancer is needed
- **TLS/HTTPS termination**: handle certificates at the edge, forward clean HTTP to the app.
- **Traffic distribution**: spread requests across multiple Laravel app containers/instances.
- **Health checks**: remove unhealthy instances automatically using a reliable probe.
- **Operational flexibility**: add/scale app instances without changing clients.

## How it fits this project
- Your backend exposes health endpoints (public, no auth):
  - `GET /health` (web)
  - `GET /api/v1/health` (API)
- Both return JSON: `{ "status": "ok" | "degraded", "checks": { "database": bool, "redis": bool } }`
- HTTP 200 when `status === "ok"`, 503 when degraded (e.g. DB or Redis unreachable)
- Sessions/caching can live in:
  - **Database** sessions (current WAMP-friendly mode), or
  - **Redis** sessions (Docker mode, depending on environment).
- Because you use shared session storage, you **do not need sticky sessions** as long as sessions are stored centrally (DB/Redis), not in local memory.

## Recommended target architecture
1. **Edge / LB**
   - HTTPS listener (443)
   - Health check: `GET /health`
2. **App layer**
   - One or more instances running:
     - **nginx** serving `backend/public`
     - proxying to **php-fpm (Laravel app)**
3. **Shared data layer**
   - MySQL (shared)
   - Redis (shared) if enabled for sessions/cache/queues

## Common deployment options
- Cloud managed LB (typical):
  - AWS ALB, GCP HTTPS LB, Azure Application Gateway
- Self-managed edge proxy:
  - HAProxy, Nginx, or Traefik in front of your Docker stack

## Health check details
- Paths: `/health` or `/api/v1/health` (use one; both are equivalent)
- Expected: HTTP 200 with `"status": "ok"`
- Degraded: HTTP 503 when database or Redis check fails
- Failure threshold: e.g. 3 consecutive failures
- Interval: e.g. 5–10 seconds

## Session / security considerations
- Use **HTTPOnly** cookies (Laravel defaults) for web sessions.
- Ensure secure cookie settings in production:
  - `SESSION_SECURE_COOKIE=true` (only over HTTPS)
- Lock CORS to the **production domain(s)** using `CORS_ALLOWED_ORIGINS`.
- Ensure refresh-token rotation + short-lived access tokens are enabled (already planned/implemented) to reduce impact of token theft.

## Checklist for revisiting later
- Confirm desired LB provider (cloud vs self-managed).
- Decide whether web sessions use DB or Redis in production.
- Confirm LB forwards:
  - correct `Host` / `X-Forwarded-Proto` headers
  - correct timeouts for long-running endpoints (if any).
- Validate:
  - `/health`
  - login redirects
  - token refresh flow
  - static page and API CORS behaviour.

