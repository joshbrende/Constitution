# 22. API — Profile & provinces

## Profile

**Controller:** `App\Http\Controllers\Api\ProfileController`

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/v1/profile` | Returns `{ "data": user }` with roles and province |
| PUT | `/api/v1/profile` | Update name, surname, national_id, province, etc. |
| DELETE | `/api/v1/profile` | Delete own account |

Requires Sanctum abilities `profile:read` / `profile:write` as appropriate. Updates are self-only (`UserPolicy`).

## Push notifications

| Method | Path | Notes |
|--------|------|-------|
| POST | `/api/v1/profile/push-token` | Register Expo push token (`profile:write`) |
| DELETE | `/api/v1/profile/push-token` | Unregister Expo token (scoped to caller) |
| POST | `/api/v1/profile/web-push-subscription` | Register PWA Web Push (VAPID) |
| DELETE | `/api/v1/profile/web-push-subscription` | Unregister Web Push endpoint |

**Ownership:** Registering a token/endpoint already owned by another user returns **409**. See [API-SECURITY.md](../API-SECURITY.md).

Env: `WEBPUSH_*` — generate with `php artisan webpush:vapid`.

## Provinces

**Controller:** `App\Http\Controllers\Api\ProvinceController`

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/v1/provinces` | List for profile picker |

## Portal notifications

**Controller:** `App\Http\Controllers\Api\PortalNotificationController`

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/v1/portal-notifications` | Inbox for auth user |
| POST | `/api/v1/portal-notifications/{notification}/read` | Mark one (other users’ IDs → 404) |
| POST | `/api/v1/portal-notifications/read-all` | Mark all |

---

*Last reviewed: 2026-07-10 — push ownership + portal notifications.*
