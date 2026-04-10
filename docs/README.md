# Documentation index (`docs/`)

| Document | Purpose |
|----------|---------|
| [**BACKEND-MOBILE-CONSISTENCY.md**](./BACKEND-MOBILE-CONSISTENCY.md) | Step-by-step backend ↔ Expo alignment: API map, auth, errors, UX split, performance, recommendations. |
| [**backend-manual/**](./backend-manual/README.md) | Canonical Laravel admin + API reference (numbered chapters). |
| [**ENVIRONMENTS.md**](./ENVIRONMENTS.md) | WAMP vs Docker, `.env` / `.env.docker`, mobile LAN URL. |
| [**DEVELOPMENT-BEST-PRACTICES.md**](./DEVELOPMENT-BEST-PRACTICES.md) | Cross-stack checklist: Laravel validation/auth/perf + Expo async/networking + mobile lint/format hygiene. |
| [**CHANGELOG.md**](./CHANGELOG.md) | User-visible changes across backend + mobile. |
| [**OPS-RUNBOOK.md**](./OPS-RUNBOOK.md) | Scheduler, cleanup, queue health, **rollback**, env vars. |
| [**PRODUCTION-HARDENING.md**](./PRODUCTION-HARDENING.md) | Pre-go-live checklist (CORS, debug, TLS, secrets). |
| **Setup Wizard** | One-time `/setup` wizard (system_admin only) that writes platform defaults to DB; includes a server `.env` checklist. See **§ Setup Wizard** in [ENVIRONMENTS.md](./ENVIRONMENTS.md). |
| [**AUDIT-LOGGING.md**](./AUDIT-LOGGING.md) | Audit log retention and queries. |
| [**CERTIFICATE-SECURITY.md**](./CERTIFICATE-SECURITY.md) | Certificate verification and abuse notes. |
| [**INPUT-SANITIZATION.md**](./INPUT-SANITIZATION.md) | User content sanitization. |
| [**LOAD-BALANCER.md**](./LOAD-BALANCER.md) | Proxy / LB configuration. |
| [**SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md**](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md) | Load, queues, and UX under pressure (conceptual). |
| [**LMS-PERFORMANCE-TRACKING.md**](./LMS-PERFORMANCE-TRACKING.md) | Academy metrics and analytics model. |
| [**membership-course-plan.md**](./membership-course-plan.md) | Membership course product plan. |
| [**progress.md**](./progress.md) | Project progress log. |
| [**DOCKER.md**](../DOCKER.md) | Repo root: Docker usage (if present). |
| **CI** | GitHub: `.github/workflows/backend-tests.yml` (PHPUnit on push/PR), `security-scan.yml` (weekly Composer + npm audits, high/critical), `codeql.yml` + `semgrep.yml` (SAST; CodeQL for JS/TS + Actions, Semgrep for PHP + JS), `dependabot.yml` (Composer + npm weekly). |

**Mobile-only deep dive:** [`../mobile/docs/OFFLINE-MOBILE.md`](../mobile/docs/OFFLINE-MOBILE.md) (offline cache architecture).

**Superseded / merged**

- `GAP-REMEDIATION.md` → merged into **§7** of [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md).
- Root `OFFLINE-MOBILE.md` → removed; use **mobile doc** + consistency guide **§5**.
