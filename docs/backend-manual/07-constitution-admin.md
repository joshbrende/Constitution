# 7. Constitution (admin CMS)

## 7.1 Purpose

Manage the **ZANU PF**, **Zimbabwe**, and **Amendment Bill** constitution documents: hierarchical **Parts → Chapters → Sections**, with **SectionVersion** history and amendment workflow.

## 7.2 Web reader (public to logged-in users)

- **Route:** `constitution.home` — `/constitution/{doc?}/{section?}`  
- **Docs:** `zanupf` | `zimbabwe` | `amendment3`  
- **Controller:** `WebConstitutionController`

Edit/Amendments links appear when `AdminAccessService::canAccessSection(..., 'constitution')`.

## 7.3 Admin routes (prefix `admin`)

| Area | Route names (pattern) |
|------|------------------------|
| Hub | `admin.constitution.index` |
| Parts | `admin.constitution.parts`, `.parts.edit`, `.parts.store`, `.parts.update`, `.parts.destroy` |
| Chapters | `admin.constitution.chapters`, `.chapters.edit`, `.chapters.store`, … |
| Sections | `admin.constitution.sections`, `.sections.edit`, `.sections.store`, `.sections.update`, `.sections.destroy` |
| Versions | `admin.constitution.sections.versions`, `.versions.create`, `.versions.store`, `.versions.edit`, `.versions.update` |
| Workflow | `admin.constitution.versions.submit`, `.versions.approve`, `.versions.reject` |

**Controller:** `App\Http\Controllers\Admin\ConstitutionController`

## 7.4 Amendment workflow (Presidium)

1. **Draft** — Section body or `SectionVersion` with `status = draft`.
2. **Submit for approval** — `versionSubmitForApproval` sets `in_review`.
3. **Approve / Reject** — Only users with **Presidium** or **System Admin** (`presidium` middleware on approve/reject). Approve → `published`; reject → `draft`.

**Direct publish bypass:** Section editor **Publish now** (only Presidium/System Admin) publishes without `in_review` — **audited** as `constitution.section_published_direct` with `presidium_review_bypassed: true`.

See [`../../backend/docs/role-workflows.md`](../../backend/docs/role-workflows.md).

## 7.5 Audit

Audit log actions (see [17-audit-logs.md](./17-audit-logs.md)):

- `constitution.version_submitted_for_review`
- `constitution.version_approved`
- `constitution.version_rejected_to_draft`
- `constitution.section_published_direct`

## 7.6 API (read-only constitution content)

Public API: `GET /api/v1/parts`, `chapters`, `sections/{section}`, `sections/search`, comments — see [26-api-public-content.md](./26-api-public-content.md).

---

*Last reviewed: documentation generation pass.*
