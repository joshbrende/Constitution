# Per-quiz facilitator stats

Facilitators and admins can view **per–Knowledge Check** stats: attempts, passed, pass rate, and average score.

## Route and controller

- **Route:** `GET /instructor/quiz-stats` → `instructor.quiz-stats`
- **Controller:** `FacilitatorDashboardController::quizStats()`
- **Auth:** `canEditCourses()` (facilitators and admins). Admins see all courses; facilitators only their `instructor_id` courses.

## Data

- **Quizzes:** `Quiz::whereIn('course_id', $courseIds)->with('course')->orderBy('title')`
- **Aggregates:** `QuizAttempt::whereIn('quiz_id', $quizIds)->selectRaw("quiz_id, count(*) as attempts, sum(case when status = 'passed' then 1 else 0 end) as passed, round(avg(percentage), 2) as avg_pct")->groupBy('quiz_id')`
- **Per row:** `attempts`, `passed`, `pass_rate` = `passed/attempts*100` (null if no attempts), `avg_pct` (null if no attempts). Quizzes with zero attempts are included.

## View

- **View:** `facilitator/quiz-stats.blade.php`
- **Table:** Course | Knowledge Check | Attempts | Passed | Pass rate | Average %
- **Empty:** "No Knowledge Checks in your courses yet. Add quiz units to courses to see per–Knowledge Check stats."

## Nav

- Facilitator sidebar: **Knowledge Check stats** (`instructor.quiz-stats`), between "Stats" and "Knowledge Check results", icon `bi-pie-chart`.
