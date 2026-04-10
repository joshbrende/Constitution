# LMS feature documentation

Feature notes and implementation details.

| Document | Description |
|----------|-------------|
| [forgot-password.md](forgot-password.md) | Forgot password: request reset link, reset form, mail, config |
| [profile.md](profile.md) | User profile: edit name/email, change password, points & badges |
| [register-restrict.md](register-restrict.md) | Register: students only; facilitator/admin via admin |
| [user-management.md](user-management.md) | Admin: list users, view user, change role |
| [attendance.md](attendance.md) | Attendance register: Day 1 unit, form in learn, store; admin view |
| [export-attendance.md](export-attendance.md) | Admin: export attendance register as CSV |
| [instructor-requests.md](instructor-requests.md) | Facilitator request to instruct; admin approve/reject |
| [facilitator-tools.md](facilitator-tools.md) | Facilitator tools audit: dashboard, stats, results, learners, submissions, ratings, chat; improvements and future ideas |
| [facilitator-ratings.md](facilitator-ratings.md) | Per-enrollment facilitator rating (1–5 + review); facilitator + admin views |
| [facilitator-chat.md](facilitator-chat.md) | Q&A: questions, replies, announcements; learn + instructor page; reply notification |
| [certificate-pdf.md](certificate-pdf.md) | Download certificate as PDF (barryvdh/laravel-dompdf) |
| [learner-dashboard.md](learner-dashboard.md) | My learning: points, badges, in progress, certificates, recent activity |
| [badge-crud.md](badge-crud.md) | Admin: create and edit badges (name, slug, points_required, icon) |
| [course-search.md](course-search.md) | Course catalog search by title/description; `q` param, order and pagination preserved |
| [course-tags.md](course-tags.md) | Course categories/tags: tags CRUD (admin), course form, catalog filter by tag; `tag`+`q`+`order` preserved |
| [quiz-true-false.md](quiz-true-false.md) | Quiz editor: True/False question type; persist `type`; MC vs TF UI and controller logic |
| [quiz-short-answer.md](quiz-short-answer.md) | Quiz editor: Short answer type; correct_text (one per line); case‑insensitive grading |
| [quiz-stats.md](quiz-stats.md) | Per–Knowledge Check facilitator stats: attempts, pass rate, average %; `instructor.quiz-stats` |
| [notifications.md](notifications.md) | In-app notifications: assignment graded, Q&A reply; bell, index, read-and-go; `ShouldQueue`; `notifications` table |
| [api.md](api.md) | Proposed JSON API layout for mobile/external integrations |
| [responsive-design.md](responsive-design.md) | Responsive design: mobile-first approach, breakpoints, sidebar navigation, table responsiveness |
| [deployment.md](deployment.md) | Deployment guide: staging/production setup, environment config, database migrations, asset optimization |
