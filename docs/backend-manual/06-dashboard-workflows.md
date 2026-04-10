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

- **`config/role_workflows.php`** — Per-role title, summary, **numbered steps** for professionalism on the Overview page.

## 6.4 Tiles

Quick links to constitution, academy, library, dialogue, party organs, etc. **Admin tiles** (analytics, priority projects) are wrapped in `@canAccessSection` where applicable.

## 6.5 Related

- [01-architecture.md](./01-architecture.md)  
- [`../../backend/docs/role-workflows.md`](../../backend/docs/role-workflows.md)  

---

*Last reviewed: documentation generation pass.*
