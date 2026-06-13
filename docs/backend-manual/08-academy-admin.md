# 8. Academy (admin)

## 8.1 Purpose

Create and maintain **courses**, **modules**, **lessons**, **assessments**, **questions**, **options**, and **achievement badges**.

Membership courses that grant party membership also require a **certificate fee** (government payment workflow).

## 8.2 Routes (prefix `admin`)

| Feature | Route name pattern |
|---------|-------------------|
| Course list | `admin.academy.index` |
| Course CRUD | `admin.academy.courses.create`, `.store`, `.edit`, `.update`, `.destroy` |
| Assessments | `admin.academy.assessments.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy` |
| Questions | `admin.academy.questions.create`, `.store`, `.edit`, `.update`, `.destroy` |
| Badges | `admin.academy.badges.index`, `.create`, `.store`, `.edit`, `.update`, `.destroy` |

**Controller:** `App\Http\Controllers\Admin\AcademyController` (courses/assessments/questions), `AcademyBadgesAdminController` (badges).

## 8.3 Membership course fees

On the course form, when **Grants membership** is enabled:

| Field | Validation |
|-------|------------|
| Certificate fee amount | Required, > 0 |
| Certificate fee currency | ISO 4217 (default USD) |
| Payment office instructions | Optional; overrides default receipt text |

Fee is **snapshotted** on the student's application at exam pass time.

Config defaults: `backend/config/academy.php` (`default_membership_fee_amount`, `payment_offices`).

## 8.4 Certificate applications (workflow)

Handled under **Certificates** admin section — not the Academy course editor.

| Feature | Route pattern |
|---------|---------------|
| Application queue | `admin.certificate-applications.index` |
| Application detail + actions | `admin.certificate-applications.show`, `.confirm-payment`, `.presidium-approve`, etc. |

**Controller:** `App\Http\Controllers\Admin\CertificateApplicationsController`

Operator runbook: [`../ACADEMY-CERTIFICATE-WORKFLOW.md`](../ACADEMY-CERTIFICATE-WORKFLOW.md).

## 8.5 Web reader

- **GET `/academy`** — `academy.home` — lists courses; **Manage** link if user has `academy` section access.

## 8.6 API (learner)

See [23-api-academy.md](./23-api-academy.md) — `AcademyCourseController`, `AcademyAssessmentController`, `CertificateApplicationController`, `AcademyAchievementsController`.

## 8.7 Caching

`AcademyCourseController` (API) caches course lists and course detail keys — after **admin** publishes or changes courses, cache TTL (e.g. 10 minutes) applies; restart cache or wait for expiry in production if needed (`php artisan cache:clear`).

## 8.8 Assessment behaviour (summary)

- Randomised question subsets, attempt binding, throttling on start/submit — implemented in `AcademyAssessmentController` (API).
- On pass for a membership course: creates `CertificateApplication` (not immediate certificate PDF for students).

---

*Last reviewed: 2026-05-23 — government certificate workflow.*
