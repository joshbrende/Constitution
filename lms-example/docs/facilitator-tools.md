# Facilitator tools – audit and reference

This document lists all facilitator (instructor) tools in the TTM Group LMS, recent improvements, and suggested future enhancements.

## Access and layout

- **Entry:** Authenticated users with `canEditCourses()` (facilitator or admin) can access the instructor area.
- **Routes:** All under `/instructor/*` (see `routes/web.php`). Dashboard: `GET /instructor`.
- **Layout:** `resources/views/layouts/facilitator.blade.php` – sidebar navigation, navbar, footer.
- **Help:** Role-specific help at `/help/facilitator` (linked in sidebar).

---

## Tool inventory

| Tool | Route | Controller / method | View | Purpose |
|------|--------|----------------------|------|---------|
| **Dashboard** | `instructor.dashboard` | `FacilitatorDashboardController@index` | `facilitator.dashboard` | Overview: course count, enrollments, **at-risk learners** (count + list by course with links), recent enrollments, pending submissions, quick actions; for non-admins: courses available to request, pending requests. |
| **Stats** | `instructor.stats` | `FacilitatorDashboardController@stats` | `facilitator.stats` | Per-course and overall: enrollments, completed, completion rate, quiz attempts, quiz pass rate. **Date range:** All time / Last 30 days / Last 90 days. Summary cards at top. |
| **Knowledge Check stats** | `instructor.quiz-stats` | `FacilitatorDashboardController@quizStats` | `facilitator.quiz-stats` | Per-quiz: attempts, passed, pass rate, average %. **Date range:** All time / Last 30 days / Last 90 days. |
| **Knowledge Check results** | `instructor.results` | `FacilitatorDashboardController@results` | `facilitator.results` | Quiz attempts: **course filter**, **paginated** (50 per page), **Export CSV** (filtered). |
| **Learners** | `instructor.course-learners` | `FacilitatorDashboardController@learners` | `facilitator.learners` | Per-course: enrollment list, progress %, units/quizzes completed, last activity, at-risk flag. **At-risk filter** with correct total count. |
| **Submissions** | `instructor.submissions.*` | `SubmissionsController` | `facilitator.submissions`, `facilitator.submission-grade` | List submissions: **course** and **status** (All / Pending / Graded) filters, paginated; grade single submission. **Pending count** in sidebar and dashboard. |
| **My ratings** | `instructor.ratings` | `FacilitatorRatingController@index` | `facilitator.ratings` | Facilitator’s own course ratings (from learners), average and count. |
| **Facilitator chat** | `instructor.facilitator-chat` | `FacilitatorChatController@instructorPage` | `facilitator.facilitator-chat` | Per-course Q&A: view and respond to learner messages. |
| **Help** | `help.facilitator` | `HelpController@facilitator` | `help.facilitator` | Facilitator help centre (topics, best practices). |
| **Instructing / Create course** | `courses.instructor`, `courses.create`, etc. | `CourseController`, `UnitController`, etc. | Various | Manage and create courses (content, units, quizzes, assignments). |

**Admin-only (when logged in as admin):** Instructor requests, Facilitator ratings (all), Attendance – linked in facilitator sidebar.

---

## Recent improvements (implemented)

1. **Learners – at-risk count**  
   `atRiskCount` is computed from all enrollments for the course *before* applying the at-risk filter, so the “At risk” badge always shows the true total.

2. **Dashboard – pending submissions**  
   Pending submissions count (status not `graded`/`returned`) is shown:
   - On the dashboard quick action “Submissions” (badge).
   - In the facilitator layout sidebar (badge) via a **View Composer** so the count is computed once per request.

3. **Stats – summary and rates**  
   - Summary cards: courses, enrolled, completed, overall completion rate, quiz attempts, overall quiz pass rate.
   - Per-course table: completion rate and quiz pass rate columns.

4. **Results – course filter, pagination, CSV export**  
   - Course dropdown to filter quiz attempts by course.
   - **Paginated** (50 per page) with Bootstrap pagination links.
   - **Export CSV** (`instructor.results.export`): same course filter; columns: Learner, Email, Course, Knowledge Check, Score %, Status, Completed at.

5. **Submissions – status filter**  
   - Status dropdown: All / Pending / Graded (in addition to course filter). “Clear filters” link when any filter is active.

6. **Stats / Quiz stats – date range**  
   - **Stats:** `range` query param: All time / Last 30 days / Last 90 days. Enrollments by `enrolled_at`, completions by `completed_at`, quiz attempts by `completed_at`.
   - **Quiz stats:** Same `range` options; attempts filtered by `completed_at`.

7. **Dashboard – at-risk learners summary**  
   - **At-risk card** (when count > 0): total at-risk count, link to “View by course” (scrolls to section).
   - **At-risk by course** section: list of courses with at-risk count; each row links to that course’s Learners page with `filter=at-risk`. At-risk = progress &lt;50% and (no last activity or last activity ≥ 14 days ago).

8. **Sidebar – pending submissions badge**  
   Pending count is injected into `layouts.facilitator` by `AppServiceProvider` View Composer so facilitators see it on every facilitator page without duplicating query logic in the layout.

---

## Optional future improvements

- **Learners:** Link to email learner or “mark as contacted” for at-risk learners (if product needs it).
- **Notifications:** Notify facilitator when a new submission is submitted or when a learner sends a facilitator-chat message (if not already covered).
- **Accessibility:** Ensure all facilitator tables and filters have appropriate labels and ARIA for screen readers.

---

## Related docs

- `docs/facilitator-chat.md` – Q&A / facilitator chat.
- `docs/facilitator-ratings.md` – Facilitator rating flow.
- `docs/quiz-stats.md` – Knowledge Check stats.
- `docs/instructor-requests.md` – Requesting to instruct a course.
- `resources/views/help/facilitator.blade.php` – In-app facilitator help.
