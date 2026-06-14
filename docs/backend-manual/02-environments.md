# 2. Environments & deployment

## 2.1 Documentation map

Detailed environment notes for this project live in the repo root docs:

| Document | Topics |
|----------|--------|
| [`../ENVIRONMENTS.md`](../ENVIRONMENTS.md) | Stages, URLs, env expectations |
| [`../OPS-RUNBOOK.md`](../OPS-RUNBOOK.md) | Operations procedures, **bad deployment & rollback** |
| [`../LOAD-BALANCER.md`](../LOAD-BALANCER.md) | If behind LB/proxy |

Always align `APP_URL`, `APP_ENV`, and database credentials with the target environment.

## 2.2 Essential `.env` keys (backend)

| Variable | Purpose |
|----------|---------|
| `APP_KEY` | Encryption (required); `php artisan key:generate` |
| `APP_URL` | Canonical URL for links, Sanctum if applicable |
| `DB_*` | Database connection |
| `AUDIT_LOG_RETENTION_DAYS` | See `config/operations.php` — audit pruning |

## 2.3 Local development (e.g. WAMP)

- Point virtual host or `php artisan serve` to `backend/public`.
- Run migrations: `php artisan migrate`
- Seed roles: `php artisan db:seed --class=RoleSeeder`

## 2.5 Setup wizard (production / first run)

For new servers, prefer the web wizard over manual `db:seed`:

| Step | Route | Action |
|------|-------|--------|
| 1 | `GET /setup` | Welcome |
| 2 | `GET /setup/checks` | Environment checks; POST continue → migrate + create DB |
| 3 | `GET /setup/admin` | First `system_admin` |
| 4 | `GET /setup/platform` | Installation URL, org, legal links, toggles |
| 5 | `GET /setup/seed` | Required platform content seed |
| 6 | `GET /setup/finish` | Production checklist + complete |

Full detail: [`../ENVIRONMENTS.md`](../ENVIRONMENTS.md#setup-wizard-one-time). Tests: `tests/Feature/SetupWizardTest.php`.

## 2.6 Health checks

- **Web:** `GET /health`  
- **API:** `GET /api/v1/health`  

Use for uptime monitors; no authentication required.

## 2.7 Assets & frontend build

- Dashboard may use Vite (`resources/js`, `resources/css`) when `public/build` or hot file exists; otherwise inline styles in Blade apply.

---

*Last reviewed: documentation generation pass.*
