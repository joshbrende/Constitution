# Progressive Web App (PWA)

Browser/installable member client in `PWA/`. Shares the same Laravel API as the Expo mobile app (`/api/v1`).

| Item | Value |
|------|-------|
| Source | `PWA/` |
| Build output | `backend/public/app/` |
| Served at | `https://{host}/app/` (nginx `location /app/`) |
| Stack | React + Vite + React Router + Tailwind v4 + axios + vite-plugin-pwa |
| API base | `VITE_API_BASE_URL` (default `/api/v1` — same origin) |
| Auth | Sanctum bearer + refresh (mirrors mobile `authStorage`) |

Design notes: [superpowers/specs/2026-07-08-pwa-design.md](./superpowers/specs/2026-07-08-pwa-design.md).  
API alignment: [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md) (treat PWA as a third client alongside Expo).

---

## Build & deploy

```bash
cd PWA
npm ci
npm run build   # writes to backend/public/app/
```

Docker/nginx already serves `backend/public/app` at `/app/`. After rebuilds on iOS Safari, clear site data or use a private tab if a stale service worker serves old hashed bundles.

**nginx expectations** (`nginx.conf`):

- `/app/index.html` — no-cache
- `/app/assets/*` — long-cache, immutable
- FastCGI read timeout ≥ 120s for cold PHP on Windows bind mounts

---

## Feature parity (vs mobile)

| Area | Status | Notes |
|------|--------|-------|
| Auth (login / register / forgot / refresh / logout) | Done | Token bootstrap before React effects |
| Home banners + tiles | Done | Carousel; tiles honour `authOnly` / `dialogueOnly` |
| Constitution reader + offline cache | Done | IndexedDB via `offline/` |
| Bookmarks / highlights | Done | Local reader data |
| Academy (courses, enrol, assessments, receipts) | Done | Lesson deep-link loads course if nav state missing |
| Library / Party / Organs / Presidium | Done | |
| Priority projects | Done | Auth required; `GET …/{id}` for deep links |
| Dialogue / chat | Done | Polling always; live via Reverb when enabled |
| Portal notifications | Done | Deep links via `notificationNavigation.js` |
| Profile + provinces | Done | |
| Web Push | Done | Profile opt-in; needs VAPID keys in `.env` |
| Workflow icons | Done | All screens use `WorkflowIcon` + `workflowIcons.js` |

---

## App config feature flags

`GET /api/v1/app-config` drives client behaviour via `AppConfigContext`:

| Flag | Effect in PWA |
|------|----------------|
| `features.enable_dialogue` | Hides Chat tile/tab/menu; chat routes redirect home |
| `realtime.*` | Reverb/Pusher connection for live chat |
| `webpush.enabled` + `public_key` | Enables browser push registration |

When `realtime.host` is `localhost` but the page is opened from a LAN IP (e.g. phone → `192.168.x.x`), the PWA substitutes `window.location.hostname` so WebSockets reach the host PC. Override with `VITE_REVERB_HOST` if needed (`PWA/.env.example`).

---

## Realtime (Reverb)

1. `BROADCAST_CONNECTION=reverb` in `backend/.env`
2. Docker service `reverb` must be **Up** (image needs PHP `pcntl` — see `backend/Dockerfile`)
3. Port `REVERB_PORT` (default **8090**) reachable from clients; allow Windows Firewall inbound if testing from a phone
4. Thread UI shows **Live** when subscribed, otherwise **Polling**

PHPUnit forces `BROADCAST_CONNECTION=null` (`phpunit.xml` `force="true"`) so tests never hit Reverb.

---

## Web Push

| Env | Purpose |
|-----|---------|
| `WEBPUSH_ENABLED` | Master switch |
| `WEBPUSH_SUBJECT` | VAPID subject (`mailto:`…) |
| `WEBPUSH_PUBLIC_KEY` / `WEBPUSH_PRIVATE_KEY` | VAPID key pair |

Generate keys:

```bash
docker compose exec app php artisan webpush:vapid
```

Register/unregister: `POST`/`DELETE /api/v1/profile/web-push-subscription` (ownership-safe — see [API-SECURITY.md](./API-SECURITY.md)).

---

## Icons

Semantic keys live in `PWA/src/ui/icons/workflowIcons.js` (mirrors `mobile/src/ui/icons/`). Screens must use `<WorkflowIcon iconKey="…" />`, not raw Lucide imports. See [ui-icons/WORKFLOW-ICONS.md](./ui-icons/WORKFLOW-ICONS.md).

---

## Local phone testing

1. `docker compose up -d` (include `reverb` for live chat)
2. Open `http://{LAN-IP}:8081/app/` on the device (not `localhost`)
3. Ensure `CORS_ALLOWED_ORIGINS` includes that origin if you ever point the API cross-origin (same-origin `/app` + `/api` needs no CORS)
4. Hard-refresh after each PWA rebuild

---

*Last updated: 2026-07-10 — PWA integration + security hardening pass.*
