# LMS Performance Tracking

**Purpose**: Define how the Academy tracks learner progress, assessment performance, and completion metrics. Supports admin reporting, learner dashboards, and external analysis via exports.

**Related**: [membership-course-plan.md](membership-course-plan.md)

---

## 1. Data Model

### Existing

| Entity | Key fields |
|--------|------------|
| `enrolments` | `user_id`, `course_id`, `status`, `completed_at` |
| `assessment_attempts` | `assessment_id`, `user_id`, `score`, `status`, `started_at`, `submitted_at` |
| `assessments` | `course_id`, `pass_mark`, `status` |

- **Score**: Percentage (0–100) stored on `assessment_attempts` when `status = 'graded'`.
- **Pass/fail**: Computed per attempt as `score >= assessment.pass_mark` (default 70%).
- **Status**: `enrolled` / `in_progress` / `completed`; `in_progress` / `submitted` / `graded`.

---

## 2. Metrics

### Admin analytics (dashboard)

| Metric | Description | Source |
|--------|-------------|--------|
| Total assessment attempts | All graded attempts | `assessment_attempts` where `status = 'graded'` |
| Pass rate | % of graded attempts that passed | `score >= assessment.pass_mark` / total graded |
| Average score | Mean score across graded attempts | `AVG(score)` where graded |
| Attempts by month | Time series for last 6 months | Group by `submitted_at` |

### Learner API (academy summary)

| Metric | Description | Source |
|--------|-------------|--------|
| `assessment_attempts_count` | Total graded attempts by user | Count for user |
| `average_score` | Mean score across user's graded attempts | `AVG(score)` |
| `passed_attempts` | Count of attempts where passed | `score >= pass_mark` |
| `pass_rate` | % passed (0–100 or null if no attempts) | `passed / total_graded * 100` |

---

## 3. Exports

### Enrolments CSV

| Column | Source |
|--------|--------|
| user_id | enrolment.user_id |
| email | user.email |
| course_id | enrolment.course_id |
| course_title | course.title |
| status | enrolment.status |
| enrolled_at | enrolment.created_at |
| completed_at | enrolment.completed_at |

### Assessment attempts CSV

| Column | Source |
|--------|--------|
| attempt_id | assessment_attempts.id |
| user_id | assessment_attempts.user_id |
| email | user.email |
| assessment_id | assessment_attempts.assessment_id |
| assessment_title | assessment.title |
| course_title | course.title |
| score | assessment_attempts.score |
| passed | score >= assessment.pass_mark |
| started_at | assessment_attempts.started_at |
| submitted_at | assessment_attempts.submitted_at |

---

## 4. API Endpoints

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/academy/summary` | GET | Bearer | Learner summary + performance (attempts, avg score, pass rate) |
| `/admin/analytics` | GET | Web admin | Dashboard with assessment stats |
| `/admin/analytics/export/enrolments` | GET | Web admin | CSV download |
| `/admin/analytics/export/attempts` | GET | Web admin | CSV download |

---

## 5. Roadmap

- [x] Spec document
- [x] Admin analytics: assessment attempts, pass rate, avg score
- [x] Academy API summary: learner performance
- [x] CSV export: enrolments, attempts
- [ ] Per-course or per-assessment breakdown in admin (optional)
- [ ] Mobile app display of learner performance summary (optional)
