# Can this application be rebuilt from documentation alone?

**Short answer: No.** If you lose the source code and retain only `docs/`, you cannot reproduce the running product at its current level of detail. Documentation describes **intent, architecture, workflows, and operations** — not every line of implementation.

**What you must preserve for disaster recovery:**

| Priority | Artifact | Why |
|----------|----------|-----|
| **1** | Git repository (`backend/`, `mobile/`, `docker-compose.yml`, migrations) | Source of truth for logic, schema, UI, tests |
| **2** | Database backup | Live content, users, audit logs, certificate applications |
| **3** | `storage/app/public` backup | Uploads, official PDFs, generated certificates |
| **4** | `.env` / hosting secrets | Keys, DB, mail, CORS (never commit) |
| **5** | `docs/` | Operator runbooks, RBAC, rebuild orientation |

Treat `docs/` as the **map**, not the **factory**.

---

## What docs alone CAN support (~40–50% of a rebuild)

You could re-implement **at a high level**:

- Product scope (constitution reader, academy, dialogue, certificates, admin CMS)
- Role model and admin section access ([RBAC-MATRIX.md](./RBAC-MATRIX.md))
- Government certificate workflow ([ACADEMY-CERTIFICATE-WORKFLOW.md](./ACADEMY-CERTIFICATE-WORKFLOW.md), [CERTIFICATE-STATE-MACHINE.md](./CERTIFICATE-STATE-MACHINE.md))
- Environment and deployment ([ENVIRONMENTS.md](./ENVIRONMENTS.md), [DOCKER.md](../DOCKER.md))
- Setup wizard UX and production checklist ([ENVIRONMENTS.md § Setup Wizard](./ENVIRONMENTS.md#setup-wizard-one-time))
- API route surface ([generated/api-routes.json](./generated/api-routes.json))
- Database table inventory ([DATA-MODEL.md](./DATA-MODEL.md))
- Security and audit requirements ([PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md), [AUDIT-LOGGING.md](./AUDIT-LOGGING.md))

---

## What docs alone CANNOT reproduce

| Gap | Impact |
|-----|--------|
| **67 migrations** (column-level detail) | Schema must be re-derived or rewritten |
| **~110 Blade views** | Admin UI and setup wizard layout |
| **~27 service classes** | Business rules (PDF coords, assessment scoring, audit hash chain) |
| **Seed content** | Full constitution text, 100+ assessment questions, banner copy |
| **Mobile app (Expo)** | Screens, navigation, offline cache — see [MOBILE-APP.md](./MOBILE-APP.md) |
| **PDF templates** | Certificate and payment receipt layouts (TCPDF) |
| **Tests (~30 feature tests)** | Behavioural contracts |

Rebuilding from docs would take **months** of expert work and would still diverge from the current product without the git history.

---

## Reconstruction playbook (if source is lost)

1. **Restore from backup first** — prefer git remote + DB dump + storage over rewriting.
2. Read [SOURCE-INVENTORY.md](./SOURCE-INVENTORY.md) for repository layout.
3. Recreate Laravel app skeleton; apply schema from [DATA-MODEL.md](./DATA-MODEL.md) + migration filenames in git backup if any.
4. Implement API using [generated/api-routes.json](./generated/api-routes.json) and backend-manual chapters 20–26.
5. Implement RBAC from [RBAC-MATRIX.md](./RBAC-MATRIX.md) + `config/admin.php` (if recovered).
6. Seed minimal data using [SEED-DATA-INVENTORY.md](./SEED-DATA-INVENTORY.md); constitution content requires separate legal source files.
7. Run setup wizard flow per [ENVIRONMENTS.md](./ENVIRONMENTS.md).
8. Rebuild mobile against API using [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md) + [MOBILE-APP.md](./MOBILE-APP.md).

---

## Keeping docs rebuild-ready

Regenerate machine-readable exports when routes or schema change significantly:

```bash
# From Docker (recommended)
docker compose exec app php artisan route:list --json > docs/generated/api-routes.json
```

After major releases, update:

- [CHANGELOG.md](./CHANGELOG.md)
- [DATA-MODEL.md](./DATA-MODEL.md) (if new tables)
- [CERTIFICATE-STATE-MACHINE.md](./CERTIFICATE-STATE-MACHINE.md) (if workflow changes)
- Backend manual chapters marked **draft** in [backend-manual/README.md](./backend-manual/README.md)

---

## Related documents

| Document | Purpose |
|----------|---------|
| [SOURCE-INVENTORY.md](./SOURCE-INVENTORY.md) | Repo layout and code counts |
| [DATA-MODEL.md](./DATA-MODEL.md) | Table catalog |
| [SEED-DATA-INVENTORY.md](./SEED-DATA-INVENTORY.md) | What seeders produce |
| [MOBILE-APP.md](./MOBILE-APP.md) | Expo client structure |
| [backend-manual/](./backend-manual/README.md) | Admin + API reference |
