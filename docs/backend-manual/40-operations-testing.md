# 40. Operations, security & testing

## 40.1 Operations

| Document | Content |
|----------|---------|
| [`../OPS-RUNBOOK.md`](../OPS-RUNBOOK.md) | Runbook |
| [`../ENVIRONMENTS.md`](../ENVIRONMENTS.md) | Environment matrix |
| [`../LOAD-BALANCER.md`](../LOAD-BALANCER.md) | Load balancer notes |

## 40.2 Cleanup command

**Command:** `php artisan operations:cleanup-security-data` (see `CleanupSecurityDataCommand`)

- Prunes old `audit_logs` per `AUDIT_LOG_RETENTION_DAYS` / `config/operations.php`

## 40.3 Security references

| Document | Topic |
|----------|--------|
| [`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md) | Certificate verification |
| [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md) | Audit queries and retention |
| [`../INPUT-SANITIZATION.md`](../INPUT-SANITIZATION.md) | Input handling |

## 40.4 Testing

- **Framework:** PHPUnit (`phpunit.xml`)
- **Run:** `cd backend && php artisan test` (requires **PHP ≥ 8.2**, e.g. Laravel Sail: `docker compose exec laravel.test php artisan test`)
- **Locations:** `tests/Feature`, `tests/Unit`

Coverage includes example tests, academy assessment submission, certificate admin flows, registration roles, **constitution official document API** (`ConstitutionOfficialDocumentApiTest`) — expand as features grow.

## 40.5 Gap remediation & production checklist

| Document | Purpose |
|----------|---------|
| [`../BACKEND-MOBILE-CONSISTENCY.md`](../BACKEND-MOBILE-CONSISTENCY.md) | Cross-stack review + historical gap remediation (§7) |
| [`../PRODUCTION-HARDENING.md`](../PRODUCTION-HARDENING.md) | `APP_DEBUG`, CORS, HTTPS, secrets, `EXPO_PUBLIC_API_BASE_URL`, storage |

---

*Last reviewed: gap remediation pass.*
