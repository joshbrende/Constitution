# 8. Academy (admin)

## 8.1 Purpose

Create and maintain **courses**, **modules**, **lessons**, **assessments**, **questions**, **options**, and **achievement badges**.

## 8.2 Routes (prefix `admin`)

| Feature | Route name pattern |
|---------|-------------------|
| Course list | `admin.academy.index` |
| Course CRUD | `admin.academy.courses.create`, `.store`, `.edit`, `.update`, `.destroy` |
| Assessments | `admin.academy.assessments.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy` |
| Questions | `admin.academy.questions.create`, `.store`, `.edit`, `.update`, `.destroy` |
| Badges | `admin.academy.badges.index`, `.create`, `.store`, `.edit`, `.update`, `.destroy` |

**Controller:** `App\Http\Controllers\Admin\AcademyController` (courses/assessments/questions), `AcademyBadgesAdminController` (badges).

## 8.3 Web reader

- **GET `/academy`** — `academy.home` — lists courses; **Manage** link if user has `academy` section access.

## 8.4 API (learner)

See [23-api-academy.md](./23-api-academy.md) — `AcademyCourseController`, `AcademyAssessmentController`, `AcademyAchievementsController`.

## 8.5 Caching

`AcademyCourseController` (API) caches course lists and course detail keys — after **admin** publishes or changes courses, cache TTL (e.g. 10 minutes) applies; restart cache or wait for expiry in production if needed (`php artisan cache:clear`).

## 8.6 Assessment behaviour (summary)

- Randomised question subsets, attempt binding, throttling on start/submit — implemented in `AcademyAssessmentController` (API).

---

*Last reviewed: documentation generation pass.*
