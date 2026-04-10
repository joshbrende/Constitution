# ZANU PF Constitution Platform — Backend manual

This folder is the **canonical backend documentation** for administrators, developers, and compliance staff.

**Master plan:** [PLAN.md](./PLAN.md)

---

## How to read

1. **Foundation** (01–03) — architecture, environments, authentication  
2. **Administration** (04–17) — RBAC, roles, dashboard, every admin module  
3. **API** (20–26) — mobile/integrator HTTP reference  
4. **Services & ops** (30, 40) — shared services, operations, testing  
5. **Appendices** — route map, config & glossary  

---

## Table of contents

| # | Document | Description |
|---|----------|-------------|
| — | [PLAN.md](./PLAN.md) | Original scope and maintenance plan |
| 01 | [01-architecture.md](./01-architecture.md) | Stack, directories, guards |
| 02 | [02-environments.md](./02-environments.md) | Deploy, `.env`, health |
| 03 | [03-authentication.md](./03-authentication.md) | Web + API auth |
| 04 | [04-admin-rbac.md](./04-admin-rbac.md) | Middleware, `config/admin.php` |
| 05 | [05-roles-users.md](./05-roles-users.md) | Roles, Users, Members |
| 06 | [06-dashboard-workflows.md](./06-dashboard-workflows.md) | Overview dashboard |
| 07 | [07-constitution-admin.md](./07-constitution-admin.md) | Constitution CMS + workflow |
| 08 | [08-academy-admin.md](./08-academy-admin.md) | Academy admin |
| 09 | [09-library-admin.md](./09-library-admin.md) | Digital library admin |
| 10 | [10-party-content.md](./10-party-content.md) | Party, organs, leagues, Presidium |
| 11 | [11-dialogue-admin.md](./11-dialogue-admin.md) | Dialogue moderation |
| 12 | [12-projects-banners.md](./12-projects-banners.md) | Priority projects, home banners |
| 13 | [13-static-pages.md](./13-static-pages.md) | Static pages |
| 14 | [14-members-users.md](./14-members-users.md) | Members & users screens |
| 15 | [15-certificates-admin.md](./15-certificates-admin.md) | Certificates admin + verify |
| 16 | [16-analytics.md](./16-analytics.md) | Analytics & exports |
| 17 | [17-audit-logs.md](./17-audit-logs.md) | Audit log UI and events |
| 18 | [18-web-reader-features.md](./18-web-reader-features.md) | Web constitution, library, party, verify (non-admin) |
| 20 | [20-api-overview.md](./20-api-overview.md) | API conventions |
| 21 | [21-api-authentication.md](./21-api-authentication.md) | API auth endpoints |
| 22 | [22-api-profile-provinces.md](./22-api-profile-provinces.md) | Profile & provinces |
| 23 | [23-api-academy.md](./23-api-academy.md) | Academy API |
| 24 | [24-api-certificates.md](./24-api-certificates.md) | Certificates API |
| 25 | [25-api-dialogue.md](./25-api-dialogue.md) | Dialogue API |
| 26 | [26-api-public-content.md](./26-api-public-content.md) | Library, party, constitution API, etc. |
| 30 | [30-services.md](./30-services.md) | Core PHP services |
| 40 | [40-operations-testing.md](./40-operations-testing.md) | Ops, security refs, tests |
| A1 | [appendix-routes.md](./appendix-routes.md) | Route index reference |
| A2 | [appendix-config-glossary.md](./appendix-config-glossary.md) | Config & glossary |

---

## Related documents (repository root)

| Path | Topic |
|------|--------|
| [`../BACKEND-MOBILE-CONSISTENCY.md`](../BACKEND-MOBILE-CONSISTENCY.md) | Backend ↔ mobile API map, UX, performance, archived gap notes |
| [`../README.md`](../README.md) | Index of all `docs/` files |
| [`../PRODUCTION-HARDENING.md`](../PRODUCTION-HARDENING.md) | Pre-production checklist (CORS, TLS, secrets) |
| [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md) | Audit retention & SQL |
| [`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md) | Certificate security |
| [`../ENVIRONMENTS.md`](../ENVIRONMENTS.md) | Environments |
| [`../OPS-RUNBOOK.md`](../OPS-RUNBOOK.md) | Operations |
| [`../../backend/docs/role-workflows.md`](../../backend/docs/role-workflows.md) | Amendment workflow |

---

**Status:** All chapters above are **draft** full-text passes generated from the codebase; review with each major release.
