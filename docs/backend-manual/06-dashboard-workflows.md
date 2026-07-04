# 6. Dashboard (Overview) & workflows

## 6.1 Route

- **GET `/dashboard`** — `DashboardController` → view `dashboard.blade.php`

## 6.2 Data shown

| Data | Source |
|------|--------|
| Learner KPIs (enrolments, attempts, certificates counts) | Eloquent aggregates in controller |
| Role workflow panels | `DashboardWorkflowService::getWorkflowPanelsForUser()` + `config/role_workflows.php` |
| **Action** alerts (drafts / in-review amendments, draft courses) | `getAlertLinesForUser()` + `getPendingCounts()` |
| Amendment pipeline line | `SectionVersion` counts + link to `admin.constitution.index` |

## 6.3 Configuration

- **`config/role_workflows.php`** — Per-role title, summary, **numbered steps** for professionalism on the Overview page. Includes **provincial_admin** (certificate payment confirmation) and **presidium** (certificate + constitution approval).

## 6.4 In-app help

- **Documentation** (`admin.guide.documentation`) — module map, pilot province table when `pilot_phase` site setting is set (v1.1.0+).
- **Help** (`admin.guide.help`) — provincial and Presidium quick cards.
- Full pilot chapter: [19-provincial-pilot-rollout.md](./19-provincial-pilot-rollout.md).

## 6.5 Tiles

Quick links to constitution, academy, library, dialogue, party organs, etc. **Admin tiles** (analytics, priority projects) are wrapped in `@canAccessSection` where applicable.

## 6.6 Related

- [01-architecture.md](./01-architecture.md)  
- [`../../backend/docs/role-workflows.md`](../../backend/docs/role-workflows.md)  

---

*Last reviewed: documentation generation pass.*
