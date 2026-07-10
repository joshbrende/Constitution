# 3. Authentication

## 3.1 Web (Blade)

| Route | Method | Purpose |
|-------|--------|---------|
| `login` | GET/POST | Login form and credential check (`throttle:auth-login` on POST) |
| `register` | GET/POST | Self-registration (`throttle:auth-register` on POST) |
| `password.request` / `password.email` | GET/POST | Forgot password (`throttle:auth-password-email` on POST) |
| `password.reset` / `password.update` | GET/POST | Complete password reset; revokes Sanctum + refresh tokens |
| `logout` | POST | Session logout |

**Controller:** `App\Http\Controllers\WebAuthController`

**Audit:** Login/register/logout/password_reset events logged via `AuditLogger` (see [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md) — `auth.web.*` actions).

## 3.2 API (Sanctum)

Base path: `/api/v1`

| Route | Middleware | Purpose |
|-------|--------------|---------|
| `POST auth/register` | `throttle:auth-register` | Create user; returns access + refresh tokens |
| `POST auth/login` | `throttle:auth-login` | Issue tokens; revokes prior sessions |
| `POST auth/forgot-password` | `throttle:3,60` | Reset request (email link uses web `password.reset`) |
| `POST auth/refresh` | `throttle:10,60` | Rotate refresh token (family + reuse kill) |
| `POST auth/logout` | `auth:sanctum` | Revoke current access + all refresh tokens |

**Controller:** `App\Http\Controllers\AuthController`

**Refresh hardening:** Each login starts a `family_id`. Rotation keeps the family. Presenting a previously rotated refresh token revokes the **entire family** and all Sanctum access tokens (`auth.api.refresh_reuse_detected`).

**Audit:** `auth.api.*` actions in `audit_logs` (registered, logged_in, logged_out, login_failed, refresh, password reset, etc.).

## 3.3 Rate limiting

- Forgot password: tight throttle on route definition + web `auth-password-email`.
- Refresh: `throttle:10,60`.
- Web login/register: named limiters `auth-login` / `auth-register`.
- Named limiters in `AppServiceProvider`: `certificates`, `assessments`, `certificate-verify` — applied on specific API routes.

## 3.4 Password policy

Default rule (`Password::defaults()`): minimum 8 characters, mixed case, and at least one number. Applied on API/web register, password reset, setup admin, and backend invitations.

## 3.5 Related

- [01-architecture.md](./01-architecture.md) — guards overview  
- [17-audit-logs.md](./17-audit-logs.md) — viewing auth events in admin  

---

*Last reviewed: documentation generation pass.*
