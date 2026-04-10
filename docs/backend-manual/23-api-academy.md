# 23. API — Academy

All routes below require **Sanctum** unless stated.

## Courses & summary

**Controller:** `App\Http\Controllers\Api\AcademyCourseController`

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/v1/academy/courses` | List published courses (cached) |
| GET | `/api/v1/academy/courses/membership` | Membership course |
| GET | `/api/v1/academy/summary` | Overview stats + province rank context |
| GET | `/api/v1/academy/courses/{course}` | Course detail |
| POST | `/api/v1/academy/courses/{course}/enrol` | Enrol |
| GET | `/api/v1/academy/courses/{course}/enrolment` | Enrolment status |

## Assessments

**Controller:** `App\Http\Controllers\Api\AcademyAssessmentController`

| Method | Path | Throttle |
|--------|------|----------|
| GET | `/api/v1/academy/assessments/{assessment}` | — |
| POST | `/api/v1/academy/assessments/{assessment}/attempts` | `assessments` |
| POST | `/api/v1/academy/attempts/{attempt}/submit` | `assessments` |

Anti-cheat: question subsets, tokens — see controller source.

## Achievements

**Controller:** `AcademyAchievementsController`

| Method | Path |
|--------|------|
| GET | `/api/v1/academy/badges` |

## Audit

`academy.attempt_started`, `academy.attempt_submitted` — [17-audit-logs.md](./17-audit-logs.md).

---

*Last reviewed: documentation generation pass.*
