# Appendix A — Route index (reference)

> **Note:** Regenerate from the application with `php artisan route:list` when routes change. Below is a structural summary.

## Web (`routes/web.php`)

| Prefix / area | Auth | Name prefix |
|---------------|------|-------------|
| `/` | — | welcome |
| `/verify-certificate` | throttle | `certificate.verify` |
| `/health` | — | `health` |
| `/login`, `/register`, `/password/*` | guest | `login`, `register`, `password.*` |
| `/logout` | auth | `logout` |
| `/dashboard` | auth | `dashboard` |
| `/constitution/...` | auth | `constitution.home` |
| `/academy`, `/library`, `/party`, `/party-organs`, `/certificate-preview`, `/dialogue` | auth | various |
| `/admin` | auth + admin | `admin.*` |

## API (`routes/api.php`)

Prefix: **`/api/v1`**

| Group | Middleware |
|-------|------------|
| `auth/register`, `login`, `forgot-password`, `refresh` | throttles on some |
| Most learner features | `auth:sanctum` |
| Library, party-organs, presidium, party profile, home-banners, health, pages, constitution GETs | public (POST comments needs sanctum) |

Full detail: see [20-api-overview.md](./20-api-overview.md) through [26-api-public-content.md](./26-api-public-content.md).

---

*Last reviewed: documentation generation pass.*
