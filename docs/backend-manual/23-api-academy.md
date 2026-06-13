# 23. API — Academy

All routes below require **Sanctum** unless stated.

## Courses & summary

**Controller:** `App\Http\Controllers\Api\AcademyCourseController`

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/v1/academy/courses` | List published courses (cached) |
| GET | `/api/v1/academy/courses/membership` | Membership course |
| GET | `/api/v1/academy/summary` | Overview stats, province rank, **portal_messages**, application status |
| GET | `/api/v1/academy/courses/{course}` | Course detail |
| POST | `/api/v1/academy/courses/{course}/enrol` | Enrol |
| GET | `/api/v1/academy/courses/{course}/enrolment` | Enrolment status |

**Summary fields (workflow):** `pending_payment_applications`, `latest_application_status`, `latest_application_status_label`, `latest_receipt_number`, `portal_messages[]` (`type`, `title`, `body`, `receipt_number`, `at`, `read`).

## Certificate applications (government workflow)

**Controller:** `App\Http\Controllers\Api\CertificateApplicationController`  
**Policy:** `CertificateApplicationPolicy` — owner only (`user_id`); 404 if not owned.

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/v1/academy/applications` | List user's applications |
| GET | `/api/v1/academy/applications/{application}` | Detail: receipt codes, timeline, offices, portal message |
| GET | `/api/v1/academy/applications/{application}/receipt.pdf` | Payment receipt PDF (not membership certificate) |

Requires `abilities:academy:read`.

## Assessments

**Controller:** `App\Http\Controllers\Api\AcademyAssessmentController`

| Method | Path | Throttle |
|--------|------|----------|
| GET | `/api/v1/academy/assessments/{assessment}` | — |
| POST | `/api/v1/academy/assessments/{assessment}/attempts` | `assessments` |
| POST | `/api/v1/academy/attempts/{attempt}/submit` | `assessments` |

Anti-cheat: question subsets, tokens — see controller source.

On pass (membership course): `CertificateApplicationService::createFromPassedAttempt` — member role deferred until payment confirmed (default).

## Achievements

**Controller:** `AcademyAchievementsController`

| Method | Path |
|--------|------|
| GET | `/api/v1/academy/badges` |

## Audit

`academy.attempt_started`, `academy.attempt_submitted`, `academy.application.*` — [17-audit-logs.md](./17-audit-logs.md) and [`../AUDIT-LOGGING.md`](../AUDIT-LOGGING.md).

## Operator reference

[`../ACADEMY-CERTIFICATE-WORKFLOW.md`](../ACADEMY-CERTIFICATE-WORKFLOW.md)

---

*Last reviewed: 2026-05-23 — government certificate workflow.*
