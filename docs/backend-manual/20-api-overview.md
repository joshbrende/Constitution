# 20. API overview

## 20.1 Base URL

All JSON API routes are prefixed:

```
/api/v1/
```

Configure mobile/base URL as `{APP_URL}/api/v1` (trailing slash optional per client).

## 20.2 Conventions

| Topic | Convention |
|-------|------------|
| Content-Type | `application/json` for POST bodies |
| Auth | `Authorization: Bearer {token}` for Sanctum |
| Success | Typically `{ "data": ... }` for resources |
| Errors | JSON: `message`, optional `errors` (validation), and stable `error` code (`not_found`, `validation_failed`, `server_error`, …). No stack traces in API responses. |

## 20.3 Throttling

**Default:** All `routes/api.php` traffic uses the `api` rate limiter (`bootstrap/app.php` + `RateLimiter::for('api', …)`).

Named limiters in `AppServiceProvider` (stricter endpoints stack on top of the default):

- `certificates` — certificate generate/download
- `assessments` — assessment start/submit
- `certificate-verify` — public verify page (web)

Route-level: e.g. `auth/forgot-password` `throttle:3,60`, `auth/refresh` `throttle:10,60`.

## 20.4 Chapter index

| Ch | Topic |
|----|--------|
| [21](./21-api-authentication.md) | Register, login, logout, refresh, forgot password |
| [22](./22-api-profile-provinces.md) | Profile, provinces |
| [23](./23-api-academy.md) | Academy, achievements |
| [24](./24-api-certificates.md) | Certificates PDF |
| [25](./25-api-dialogue.md) | Dialogue |
| [26](./26-api-public-content.md) | Library, party, organs, presidium, banners, pages, health, constitution |

---

*Last reviewed: documentation generation pass.*
