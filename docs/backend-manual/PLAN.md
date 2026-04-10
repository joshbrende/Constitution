# Backend documentation — master plan

This document is the **plan** for building an extensive, maintainable **backend manual** (web admin + API + services + ops). It does not replace writing the chapters; it defines **structure, scope, phases, and standards** so the team can execute in order.

---

## 1. Goals

| Goal | Detail |
|------|--------|
| **Single narrative** | One place that explains *what* each feature does, *who* uses it, *how* workflows run, and *where* to configure it. |
| **Multiple audiences** | Presidium/stakeholders (high level), **admin operators** (step-by-step), **developers** (routes, models, configs), **security/compliance** (audit, access). |
| **Traceability** | Link docs to code: route names, config keys, env vars, main classes. |
| **Living documentation** | Chapters versioned in Git; update policy when features ship. |

---

## 2. Where it lives

| Path | Purpose |
|------|---------|
| `docs/backend-manual/` | **Canonical manual** — Markdown chapters (this folder). |
| `docs/backend-manual/PLAN.md` | This plan (roadmap + TOC). |
| `docs/backend-manual/README.md` | Index + “how to read” + link to chapters. |
| Existing `docs/*.md` | **Specialist refs** (e.g. `AUDIT-LOGGING.md`, `CERTIFICATE-SECURITY.md`) — **incorporate or link** from the manual; avoid duplicating long security text in two places. |
| `backend/docs/role-workflows.md` | Role & amendment workflow — **link** from admin/RBAC chapter. |

**Optional later:** generate a static site (MkDocs, VitePress, or Docusaurus) from `docs/backend-manual/` for search and PDF export. Not required for v1.

---

## 3. Proposed table of contents (chapters)

Each chapter should include: **overview**, **user journeys**, **admin UI paths**, **API summary** (if applicable), **data model touchpoints**, **configuration**, **audit/security notes**, **known limits**, **related code paths**.

### Part A — Foundation

1. **Architecture overview** — Laravel app layout, web vs API, Sanctum, session vs token, queue/cron if used.
2. **Environments & deployment** — Align with `docs/ENVIRONMENTS.md`, `.env` keys, health endpoint, WAMP/production notes.
3. **Authentication** — Web login, API register/login/refresh/logout, password reset, rate limits; link to auth audit events.

### Part B — Administration (Blade dashboard)

4. **Admin entry & navigation** — `/admin`, role-gated sections, `config/admin.php`, `AdminAccessService`, middleware (`admin.content`, `admin.section`, `presidium`).
5. **Roles & access control** — All roles (seeded + custom), User → Edit roles, Roles CRUD (system admin), provincial/scoped access (current vs future).
6. **Dashboard (Overview)** — KPI tiles, role workflow cards, alerts (`DashboardWorkflowService`, `config/role_workflows.php`).
7. **Constitution (CMS)** — Parts/chapters/sections, versions, **draft → submit → Presidium approve/reject**, **direct publish** path; link to `docs/role-workflows.md` and audit actions.
8. **Academy (admin)** — Courses, modules, assessments, questions, badges, caches to invalidate.
9. **Digital Library (admin)** — Categories, documents, access rules.
10. **The Party, Party Organs, Party Leagues, Presidium** — Content models and admin screens.
11. **Dialogue (admin)** — Channels, threads, moderation actions.
12. **Priority projects & home banners** — Publishing workflow.
13. **Static pages** — Help, legal, etc.
14. **Members & users** — Difference (certificates vs all users), search, role assignment.
15. **Certificates (admin)** — Search, revoke/reinstate, verification; link **`docs/CERTIFICATE-SECURITY.md`**.
16. **Analytics & exports** — Metrics definitions (e.g. registered members = passed assessment), CSV exports, province stats service.
17. **Audit logs** — UI, actions list, constitution workflow columns, retention; link **`docs/AUDIT-LOGGING.md`** and extend with constitution events.

### Part C — HTTP API (mobile / integrations)

18. **API conventions** — Base URL `/api/v1`, JSON shape, errors, throttling.
19. **Profile & provinces** — User profile, province picker.
20. **Academy API** — Courses, enrolment, assessments, attempts, summary, achievements.
21. **Certificates API** — Preview, list, generate, download PDF; rate limits.
22. **Dialogue API** — Channels, threads, messages.
23. **Library, Party, Party Organs, Presidium, Priority projects** — Public/authenticated endpoints as implemented.

### Part D — Services & cross-cutting

24. **Certificate PDF generation** — `CertificatePdfService`, fonts, async/202 behaviour if any.
25. **Membership & certificates** — `MembershipService`, when `member` role is granted.
26. **Province analytics** — `ProvinceStatsService`, leaderboard semantics.
27. **Audit logger** — `AuditLogger`, when to add new events, PII considerations.

### Part E — Operations & compliance

28. **Operations runbook** — Link **`docs/OPS-RUNBOOK.md`**, backups, cleanup command, log retention.
29. **Security summary** — Link **`docs/INPUT-SANITIZATION.md`**, CORS, HTTPS, secrets.
30. **Testing & quality** — PHPUnit locations, how to run, what’s covered vs not.

### Appendices

- **A. Route index** — Generated or maintained table: `route name → URI → middleware → controller@method`.
- **B. Config index** — `config/admin.php`, `config/role_workflows.php`, `config/operations.php`, notable `config/*.php`.
- **C. Glossary** — ZANU PF terms, “member” vs “registered user”, assessment vs certificate.

---

## 4. Phased execution (recommended order)

| Phase | Scope | Outcome |
|-------|--------|---------|
| **0** | Create `docs/backend-manual/README.md` with full TOC (links to files, many stubs). | Navigable skeleton. |
| **1** | Parts A1–A3 + B4–B7 (architecture, auth, admin access, constitution). | Highest-risk / highest-value flows documented first. |
| **2** | B8–B17 (remaining admin). | Full admin coverage. |
| **3** | Part C (API). | Mobile/integrator reference. |
| **4** | Parts D–E + appendices. | Ops, security, indexes. |
| **5** | Consolidate overlaps with `docs/AUDIT-LOGGING.md`, `CERTIFICATE-SECURITY.md`, etc. (single source of truth + “see also”). | No contradiction. |

Estimate: **2–4 weeks** of focused technical writing for Phases 0–2, depending on depth and screenshots; API and ops chapters can follow.

---

## 5. Writing standards

- **Tone**: Neutral, professional; second person for procedures (“Open Admin → …”).
- **Screens**: Optional screenshots in `docs/backend-manual/assets/` (named by chapter).
- **Code**: Fenced blocks with language tag; prefer **route names** and **file paths** over huge code dumps.
- **Diagrams**: Mermaid in Markdown where useful (sequence for amendment approval, role → section matrix).
- **“Last reviewed”**: Optional footer per file: date + Git short hash.

---

## 6. Maintenance policy

- **Definition of done** for features: update or add a subsection in the manual (or linked doc) in the same release when behaviour changes.
- **Quarterly review**: TOC vs routes/config (script or checklist).

---

## 7. Success criteria

- [ ] A new **admin operator** can follow the manual to perform constitution, academy, and user tasks without reading PHP.
- [ ] A **developer** can locate controllers, configs, and audit actions for a feature within one chapter.
- [ ] **Compliance** can map audit log actions to documented workflows.
- [ ] No major feature area in Parts B–C remains undocumented (stub links OK only short-term).

---

## 8. Immediate next steps (checklist)

1. Add **`docs/backend-manual/README.md`** — index + placeholder links for each chapter file (`01-architecture.md`, …).
2. Create **stub files** (`00-overview.md` or per-chapter stubs) with title + “TODO” + related routes.
3. Prioritise writing **Chapter 4–7** (admin access + constitution) using existing `role-workflows` and audit docs.
4. Schedule **appendix A** (route list) — `php artisan route:list` export scripted to Markdown monthly or on release.

---

*Document version: 1.0 — planning only; chapters to be authored per phases above.*

---

## 9. Generation status (manual chapters)

A **full first pass** of manual chapters **01–07, 08–17, 20–26, 30, 40, appendices**, plus **`README.md`**, **`00-table-of-contents.md`**, was generated in-repo. Treat as **draft**: verify against `routes/web.php`, `routes/api.php`, and controllers after each release.
