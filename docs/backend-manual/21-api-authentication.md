# 21. API — Authentication

**Controller:** `App\Http\Controllers\AuthController`

| Method | Path | Auth | Notes |
|--------|------|------|-------|
| POST | `/api/v1/auth/register` | No | Creates user; assigns default role per app logic |
| POST | `/api/v1/auth/login` | No | Returns token |
| POST | `/api/v1/auth/forgot-password` | No | Throttled `3,60` |
| POST | `/api/v1/auth/refresh` | No | Throttled `10,60` |
| POST | `/api/v1/auth/logout` | Sanctum | Revokes token |

## Audit

API auth events logged to `audit_logs` (`auth.api.*`) — see [17-audit-logs.md](./17-audit-logs.md) and [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md).

---

*Last reviewed: documentation generation pass.*
