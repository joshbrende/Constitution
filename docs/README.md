# Documentation index (`docs/`)

Start here for orientation. **Disaster recovery:** read [RECONSTRUCTION.md](./RECONSTRUCTION.md) first — docs alone cannot rebuild the app without source code.

| Document | Purpose |
|----------|---------|
| [**RECONSTRUCTION.md**](./RECONSTRUCTION.md) | **Can docs alone rebuild the app?** Recovery playbook, gaps, what to back up |
| [**SOURCE-INVENTORY.md**](./SOURCE-INVENTORY.md) | Repository layout, code counts, critical paths |
| [**DATA-MODEL.md**](./DATA-MODEL.md) | Database table catalog, `site_settings` keys |
| [**SEED-DATA-INVENTORY.md**](./SEED-DATA-INVENTORY.md) | What each seeder produces |
| [**generated/api-routes.json**](./generated/api-routes.json) | Machine-readable Laravel route export |
| [**ENVIRONMENTS.md**](./ENVIRONMENTS.md) | WAMP vs Docker, Setup Wizard, CORS, mobile URL |
| [**PRODUCTION-HARDENING.md**](./PRODUCTION-HARDENING.md) | Pre-go-live checklist (CORS, debug, TLS, mail, queue, storage). |
| [**OPS-RUNBOOK.md**](./OPS-RUNBOOK.md) | Scheduler, cleanup, queue worker, **rollback**, env vars. |
| [**backend-manual/**](./backend-manual/README.md) | Canonical Laravel admin + API reference (numbered chapters). |
| [**RBAC-MATRIX.md**](./RBAC-MATRIX.md) | Unified role × section × API policy matrix. |
| [**BACKEND-MOBILE-CONSISTENCY.md**](./BACKEND-MOBILE-CONSISTENCY.md) | Backend ↔ Expo alignment: API map, auth, errors, UX, performance. |
| [**DEVELOPMENT-BEST-PRACTICES.md**](./DEVELOPMENT-BEST-PRACTICES.md) | Cross-stack checklist: Laravel + Expo hygiene. |
| [**CHANGELOG.md**](./CHANGELOG.md) | User-visible changes across backend + mobile. |
| [**ACADEMY-CERTIFICATE-WORKFLOW.md**](./ACADEMY-CERTIFICATE-WORKFLOW.md) | Operator runbook: payment → Presidium → print → collection |
| [**CERTIFICATE-STATE-MACHINE.md**](./CERTIFICATE-STATE-MACHINE.md) | Technical status enum, transitions, side effects |
| [**MOBILE-APP.md**](./MOBILE-APP.md) | Expo client structure, screens, API dependencies |
| [**AUDIT-LOGGING.md**](./AUDIT-LOGGING.md) | Audit log retention and queries. |
| [**CERTIFICATE-SECURITY.md**](./CERTIFICATE-SECURITY.md) | Certificate verification and abuse notes. |
| [**INPUT-SANITIZATION.md**](./INPUT-SANITIZATION.md) | User content sanitization. |
| [**LOAD-BALANCER.md**](./LOAD-BALANCER.md) | Proxy / LB configuration. |
| [**INTEGRATIONS.md**](./INTEGRATIONS.md) | External systems and integration notes. |
| [**politburo/**](./politburo/README.md) | **Politburo briefing pack** — executive summary, rollout plan, presentation outline |
| [**INSTALL-SECURITY.md**](./INSTALL-SECURITY.md) | Setup wizard token lock and install hardening |
| [**INCIDENT-RESPONSE.md**](./INCIDENT-RESPONSE.md) | Security incident playbooks |
| [**SOC2-READINESS-CHECKLIST.md**](./SOC2-READINESS-CHECKLIST.md) | SOC 2 readiness by TSC control (Done / Partial / Missing) |
| [**LMS-PERFORMANCE-TRACKING.md**](./LMS-PERFORMANCE-TRACKING.md) | Academy metrics and analytics model. |
| [**membership-course-plan.md**](./membership-course-plan.md) | Membership course product plan. |
| [**SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md**](./SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md) | Load, queues, and UX under pressure (conceptual). |
| [**DOCKER.md**](../DOCKER.md) | Docker Compose services, ports, first-run install. |

**Mobile:** [MOBILE-APP.md](./MOBILE-APP.md) and [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md)

**CI:** `.github/workflows/backend-tests.yml`, `security-scan.yml`, `codeql.yml`, `semgrep.yml`, `dependabot.yml`

---

## First-time server install

1. Configure `backend/.env` (or hosting env vars): `APP_KEY`, `DB_*`, optional `MAIL_*`.
2. Open **`GET /setup`** — public 6-step wizard until `installed_at` is set.
3. Complete the **Production checklist** on the finish step (mail, CORS, cron, mobile API URL).
4. See [ENVIRONMENTS.md § Setup Wizard](./ENVIRONMENTS.md#setup-wizard-one-time) and [PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md).

---

## Superseded / removed

| Was | Now |
|-----|-----|
| `GAP-REMEDIATION.md` | **§7** of [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md) |
| Root `OFFLINE-MOBILE.md` | [mobile/docs/OFFLINE-MOBILE.md](../mobile/docs/OFFLINE-MOBILE.md) |
| `progress.md` | Removed — use [CHANGELOG.md](./CHANGELOG.md) and backend-manual |
| `superpowers/plans/*.md` | Removed — implemented; see [ACADEMY-CERTIFICATE-WORKFLOW.md](./ACADEMY-CERTIFICATE-WORKFLOW.md) |
