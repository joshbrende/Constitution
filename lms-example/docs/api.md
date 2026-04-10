# JSON API layout (proposed)

This LMS is currently web‑only (Blade views + `routes/web.php`). If you later need **mobile apps** or **external integrations**, you can expose a thin JSON API that mirrors the existing features.

This document sketches a pragmatic layout; it is **documentation only** – no `routes/api.php` endpoints are wired yet.

## Auth & profile

- **GET `/api/me`** – return authenticated user, roles, basic stats (points, badges count, enrolled courses count).
- **POST `/api/login`** – email + password → token (Laravel Sanctum / Passport) + user.
- **POST `/api/logout`** – revoke current token.
- **POST `/api/register`** – create student + send email verification.
- **POST `/api/forgot-password`**, **POST `/api/reset-password`** – mirror web flows with JSON responses.
- **GET `/api/profile`**, **PUT `/api/profile`**, **PUT `/api/profile/password`** – read/update profile + password.

## Courses & enrollment

- **GET `/api/courses`** – list with filters:
  - `q` (search title/description), `tag`, `order` (newest, alpha, most_members), pagination.
- **GET `/api/courses/{slug}`** – single course + summary curriculum, rating, tags.
- **GET `/api/courses/{slug}/curriculum`** – structured curriculum (3‑day flow) as JSON.
- **POST `/api/courses/{slug}/enroll`** – enroll current user; returns enrollment + progress.
- **POST `/api/courses/{slug}/enroll-bulk`** – bulk enroll by CSV (emails) for admins/facilitators.
- **POST `/api/courses/{slug}/duplicate`** – duplicate course (admin/facilitator).

## Learning, quizzes, assignments

- **GET `/api/learn/{slug}`** – current progress summary for a course (next unit, unlocked units, percentage).
- **GET `/api/learn/{slug}/units/{unitId}`** – unit content (lesson/quiz/assignment metadata).
- **POST `/api/learn/{slug}/units/{unitId}/complete`** – mark non‑quiz, non‑assignment unit complete.
- **POST `/api/learn/{slug}/quiz/{unitId}`** – submit Knowledge Check:
  - body: `{ "answers": { "question_id": "value", ... } }`
  - response: `{ "status": "passed|failed", "score": { "raw": 8, "total": 10, "percentage": 80 }, "message": "...", "attempt": { ... } }`
- **POST `/api/learn/{slug}/assignment/{unitId}/submit`** – submit assignment (text + optional attachments).

## Attendance, certificates, dashboard

- **POST `/api/learn/{slug}/attendance`** – submit Day 1 attendance register for current enrollment.
- **GET `/api/courses/{slug}/attendance`** – admin/facilitator attendance listing.
- **GET `/api/courses/{slug}/attendance/export`** – CSV export (or JSON rows + client‑side CSV).
- **GET `/api/me/learning`** – learner dashboard (points, badges, in‑progress courses, certificates, recent activity).
- **GET `/api/leaderboard`** – gamification leaderboard data.
- **GET `/api/certificates/{id}`** – certificate metadata (course, issued_at, download URLs).

## Reviews, ratings, facilitator chat

- **POST `/api/courses/{slug}/reviews`** – create/update course review (rating + optional text).
- **POST `/api/courses/{slug}/rate-facilitator`** – per‑enrollment facilitator rating.
- **GET `/api/courses/{slug}/facilitator-chat`** – thread (questions, replies, announcements).
- **POST `/api/courses/{slug}/facilitator-chat`** – add question/reply/announcement.

## Instructor & admin utilities

- **GET `/api/instructor`** – instructor dashboard stats.
- **GET `/api/instructor/quiz-stats`** – per‑Knowledge Check stats (attempts, pass rate, average %).
- **GET `/api/instructor/results`** – list quiz/assignment results for marking/export.
- **GET `/api/instructor/ratings`** – facilitator ratings.
- **GET `/api/instructor/submissions`**, **GET `/api/instructor/submissions/{id}`**, **PUT `/api/instructor/submissions/{id}`** – assignment grading.
- **GET `/api/admin/users`**, **PUT `/api/admin/users/{id}/role`** – user management.
- **GET `/api/admin/instructor-requests`**, **POST `/api/admin/instructor-requests/{id}/approve`**, **POST `/api/admin/instructor-requests/{id}/reject`**.
- **GET `/api/admin/facilitator-ratings`** – global view of facilitator ratings.

## Notifications

- **GET `/api/notifications`** – paginated list of notifications for current user.
- **POST `/api/notifications/{id}/read`** – mark one as read.
- **POST `/api/notifications/read-all`** – mark all as read.

## Implementation notes

- Prefer **Laravel Sanctum** tokens for first version (simple SPA/mobile support).
- Keep controllers thin by reusing existing domain logic (services, models) from `routes/web.php` handlers.
- Use response resources (`JsonResource`) to keep the JSON shape stable as views evolve.

