# 3. Authentication

## 3.1 Web (Blade)

| Route | Method | Purpose |
|-------|--------|---------|
| `login` | GET/POST | Login form and credential check |
| `register` | GET/POST | Self-registration (role assignment per app rules) |
| `password.request` / `password.email` | GET/POST | Forgot password |
| `logout` | POST | Session logout |

**Controller:** `App\Http\Controllers\WebAuthController`

**Audit:** Login/register/logout events logged via `AuditLogger` (see [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md) — `auth.web.*` actions).

## 3.2 API (Sanctum)

Base path: `/api/v1`

| Route | Middleware | Purpose |
|-------|--------------|---------|
| `POST auth/register` | — | Create user; returns token per implementation |
| `POST auth/login` | — | Issue token |
| `POST auth/forgot-password` | `throttle:3,60` | Reset request |
| `POST auth/refresh` | `throttle:10,60` | Refresh token |
| `POST auth/logout` | `auth:sanctum` | Revoke current token |

**Controller:** `App\Http\Controllers\AuthController`

**Audit:** `auth.api.*` actions in `audit_logs` (registered, logged_in, logged_out, login_failed, refresh, password reset, etc.).

## 3.3 Rate limiting

- Forgot password: tight throttle on route definition.
- Refresh: `throttle:10,60`.
- Named limiters in `AppServiceProvider`: `certificates`, `assessments`, `certificate-verify` — applied on specific API routes.

## 3.4 Password policy

Handled by Laravel validation on register/update; see `AuthController` and `ProfileController` validation rules.

## 3.5 Related

- [01-architecture.md](./01-architecture.md) — guards overview  
- [17-audit-logs.md](./17-audit-logs.md) — viewing auth events in admin  

---

*Last reviewed: documentation generation pass.*
