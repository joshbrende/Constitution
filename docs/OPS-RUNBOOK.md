# Operations Runbook

Operational commands, scheduler configuration, and environment variables for production and staging.

---

## Commands

### `ops:queue-health`

Checks queue and certificate generation health. Use for monitoring and alerting.

```bash
php artisan ops:queue-health
```

**Checks:**
- Failed jobs count vs `QUEUE_HEALTH_MAX_FAILED_JOBS` (default 10)
- Certificates stuck in `pending` or `generating` longer than `QUEUE_HEALTH_STALE_CERTIFICATE_MINUTES` (default 30)

**Output:**
- Human-readable status and warnings
- Exit code `1` on failure

**Options:**
- `--json` — JSON output for monitoring integration

```bash
php artisan ops:queue-health --json
```

**Env vars (see `config/operations.php`):**
- `QUEUE_HEALTH_MAX_FAILED_JOBS` — threshold for failed jobs
- `QUEUE_HEALTH_STALE_CERTIFICATE_MINUTES` — minutes before a pending/generating certificate is considered stale

---

### `ops:cleanup-security-data`

Prunes aged audit logs and expired/revoked refresh tokens. Scheduled daily; safe to run manually.

```bash
php artisan ops:cleanup-security-data
```

**What it deletes:**
- Audit log rows older than `AUDIT_LOG_RETENTION_DAYS` (default 365)
- Refresh tokens that are expired or revoked and older than `REFRESH_TOKEN_RETENTION_DAYS` (default 30)

**Options:**
- `--dry-run` — show what would be deleted without making changes

```bash
php artisan ops:cleanup-security-data --dry-run
```

**Env vars:**
- `AUDIT_LOG_RETENTION_DAYS` — retention for audit logs (default 365)
- `REFRESH_TOKEN_RETENTION_DAYS` — retention for expired/revoked refresh tokens (default 30)

---

## Scheduler

The Laravel scheduler runs:

| Command                   | Schedule          | Purpose                       |
|---------------------------|-------------------|-------------------------------|
| `ops:queue-health`        | Every 5 minutes   | Monitor queue and PDF health  |
| `ops:cleanup-security-data` | Daily 02:15     | Prune old audit logs and tokens |
| `auth:clear-resets`       | Daily 02:30       | Remove expired password reset tokens |

**Required:** A single cron entry on the host to run the scheduler:

```bash
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

Or run the scheduler in the foreground (e.g. in a container or supervisor):

```bash
php artisan schedule:work
```

---

## Environment variables

All ops-related env vars, with defaults:

| Variable                         | Default | Description                                      |
|----------------------------------|---------|--------------------------------------------------|
| `AUDIT_LOG_RETENTION_DAYS`       | 365     | Days to retain audit logs before cleanup         |
| `REFRESH_TOKEN_RETENTION_DAYS`   | 30      | Days to retain expired/revoked refresh tokens    |
| `QUEUE_HEALTH_MAX_FAILED_JOBS`   | 10      | Failed jobs threshold for queue health warning   |
| `QUEUE_HEALTH_STALE_CERTIFICATE_MINUTES` | 30 | Minutes before pending PDF is considered stale   |

---

## Bad deployment and rollback

**Prerequisite:** Take a **database backup** before production deploys that run migrations. Rollback without a backup is often incomplete.

### 1. Limit damage immediately

- Put the app in maintenance mode (optional bearer URL for admins):

  ```bash
  php artisan down --secret="your-long-random-token"
  ```

  Staff can still open `https://your-domain/your-long-random-token` to debug. Bring traffic back:

  ```bash
  php artisan up
  ```

### 2. Roll back application code (preferred when migrations are compatible)

Redeploy the **previous known-good artifact** (Docker image tag, git release tag, or build from the prior commit). Typical sequence after the old code is live:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
# If you use route/config/view caching in prod:
php artisan optimize:clear
```

Restart PHP-FPM / queue workers / `php artisan queue:restart` so workers load the old code.

### 3. Database migrations

- **If the bad deploy only changed code** (no new migrations ran): skip this step.
- **If new migrations ran and have safe `down()` methods**, you can roll back the last batch:

  ```bash
  php artisan migrate:rollback --step=1
  ```

  Use `--step=N` to undo multiple batches. **Caveats:**
  - Migrations that **drop columns/tables** or **reshape data** may lose data on rollback or may not be reversible; test `down()` in staging.
  - If rollback is unsafe, **restore the DB from backup** instead of `migrate:rollback`.

### 4. Environment and secrets

- If `.env` or platform secrets changed, **restore the previous values** from your secrets store or backup, then `config:clear` / `optimize:clear` as above.

### 5. Mobile app (Expo / store builds)

- **Store builds:** users keep the installed binary; rollback = **submit a reverted build** or ask users to stay on a previous version until a fix ships.
- **OTA (EAS Update):** you can **republish** a prior update or **roll back** in EAS to the last good revision, depending on your channel policy.

### 6. After rollback

- Confirm `GET /health` and `GET /api/v1/health`.
- Run `php artisan ops:queue-health` and clear failed jobs if needed (`php artisan queue:flush` only if you accept losing failed job payloads for inspection).

---

## Related docs

- [AUDIT-LOGGING.md](AUDIT-LOGGING.md) — audit events, query examples, retention guidance
- [CERTIFICATE-SECURITY.md](CERTIFICATE-SECURITY.md) — certificate verification and admin runbook
