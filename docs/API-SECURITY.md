# API security — IDOR & ownership

This document records the **July 2026 IDOR audit** of `backend/` member APIs and the remediations applied. Authorization is **application-layer** (Sanctum abilities + Laravel policies + owner-scoped route binding). The stack uses **MySQL**, so Postgres-style Row Level Security (RLS) is not used — that is expected and safe when these controls hold.

Related: [politburo/SECURITY-AND-COMPLIANCE.md](./politburo/SECURITY-AND-COMPLIANCE.md), [RBAC-MATRIX.md](./RBAC-MATRIX.md), [INSTALL-SECURITY.md](./INSTALL-SECURITY.md).

---

## Audit summary (2026-07)

| Severity | Count | Outcome |
|----------|-------|---------|
| Critical / High | 0 | No cross-user read of profile, notifications, certificates, applications, or attempts by ID |
| Medium | 1 | Fixed — push / web-push subscription reassignment |
| Low | 2 | Fixed — dialogue report auth; unpublished project like oracle |
| Info | 2 | Documented — dialogue province/`is_public` docs vs code; 403/404 existence |

### Correctly protected (verified)

- **Profile** — self only (`UserPolicy`)
- **Portal notifications** — `$user->notifications()->where('id', …)` → foreign IDs 404
- **Certificate / application / assessment attempt** — `resolveRouteBinding` scopes to `auth()->id()`
- **Dialogue** — channel `canUserAccess` + thread policies; blocks scoped to blocker
- **Comments** — create only; `user_id` forced to auth user
- **Public CMS** — read-only API; unpublished → 404

---

## Remediations

### 1. Push / Web Push ownership (Medium) — fixed

**Issue:** `updateOrCreate` keyed only on Expo token / Web Push endpoint could reassign another user’s row to the caller.

**Fix:** Before upsert, if the credential exists for a different `user_id`, return **409 Conflict**. Owner may still refresh their own registration.

| Endpoint | Controllers |
|----------|-------------|
| `POST /api/v1/profile/push-token` | `PushTokenController` |
| `POST /api/v1/profile/web-push-subscription` | `WebPushSubscriptionController` |

**Tests:** `MobilePushAndPortalNotificationTest` (cannot reassign; owner can refresh).

### 2. Dialogue `reportMessage` (Low) — fixed

**Issue:** Authorize ran only when `$message->thread` was truthy.

**Fix:** `abort_unless($message->thread, 404)` then always `$this->authorize('view', $message->thread)`.

**Tests:** `DialogueReadAuthorizationTest::test_user_without_channel_role_cannot_report_message` (uses `Event::fake` so Reverb is not required).

### 3. Priority project like existence oracle (Low) — fixed

**Issue:** Liking an unpublished project returned **403**; `show` returned **404**.

**Fix:** `like()` calls `abort_unless($this->isPublished(…), 404)` before authorize.

**Tests:** `PriorityProjectLikePolicyTest` expects `assertNotFound()` for drafts.

---

## Residual notes (Info — not bugs)

1. **Dialogue access model** — Access is **role-based** (`min_role_slug`). API docs historically mentioned province / `is_public`; those are not applied in `DialogueChannel::canUserAccess`. Align product docs with code or implement the intended rules.
2. **403 vs 404** — Unauthorized access to an existing ID often returns 403 (Laravel default). Optional hardening: map forbidden → 404 to hide existence.

---

## Why no DB RLS

- Clients never query MySQL directly; only Laravel does.
- Owner isolation for private objects uses **route binding** + **policies**.
- RLS would matter for Postgres + direct client SQL (e.g. Supabase). Adding it here would require a DB engine change and duplicate policy logic.

---

## Auth hardening (2026-07)

| Control | Detail |
|---------|--------|
| Password reset completion | Web `password.reset` / `password.update`; success revokes Sanctum + refresh tokens |
| Refresh families | `refresh_tokens.family_id`; reuse of a rotated token kills the family + access tokens |
| Concurrent refresh | `lockForUpdate` inside a DB transaction |
| Password defaults | `Password::min(8)->mixedCase()->numbers()` via `Password::defaults()` |
| Web throttles | `auth-login`, `auth-register`, `auth-password-email` |
| Clients | Guest mode clears stored tokens; single-flight refresh on PWA + mobile |

**Tests:** `AuthRefreshForgotPasswordTest` (rotation, reuse kill, reset completion, weak password).

---

## Verification

```bash
docker compose exec app php artisan test --filter="MobilePushAndPortalNotificationTest|PriorityProjectLikePolicyTest|DialogueReadAuthorizationTest"
```

PHPUnit forces `BROADCAST_CONNECTION=null` (`phpunit.xml` `force="true"`) so dialogue message creates do not call Reverb during tests.

---

*Last updated: 2026-07-10.*
