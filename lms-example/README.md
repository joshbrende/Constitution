# Laravel LMS

A Laravel-based Learning Management System (LMS) inspired by the **wplms-example** (WPLMS) theme. It provides courses, curriculum (sections, units, Knowledge Checks), enrollments, and a course player.

## Features

- **Courses**: List, single view, enroll. **Search** by title/description (`q`); **filter by tag**; order by newest, alphabetical, or most members. **Tags** (admin CRUD, course form, catalog filter).
- **Curriculum**: Sections → Units (lessons, Knowledge Checks, assignments). Lesson content supports HTML.
- **Knowledge Checks**: **Multiple choice**, **true/false**, **short answer**; randomize questions; per-question correct/incorrect in results. Passing score 70%; pass-to-unlock next module. **Quiz stats** (attempts, pass rate, average %) per Knowledge Check.
- **Assignments**: Submit text + file upload; facilitator grade + feedback; **Submissions** list in instructor dashboard. **Notifications** (assignment graded, Q&A reply; bell, index; queued `ShouldQueue`).
- **Certificates**: Issued on **course completion**; simple HTML template; Print; optional PDF (`barryvdh/laravel-dompdf`).
- **Course reviews & ratings**: Star rating (1–5) + optional review text on course show; enrolled users can submit; average & count. **Facilitator ratings** (per-enrollment; facilitator + admin views).
- **Gamification**: Points (enroll +10, unit +5, Knowledge Check pass +15, course complete +50), **badges** (admin CRUD), **Leaderboard**.
- **Enrollments**: Progress tracking, unit completion, Knowledge Check attempts. **Attendance** register; **export CSV** (admin).
- **Auth**: Login, register, logout. **Forgot / reset password**. Roles: **student**, **facilitator**, **admin** (`roles` + `model_has_roles`). `instructor` = facilitator. **Profile**: edit name/email, change password.
- **Instructor requests**: Facilitators request to instruct (when `instructor_id` null); admin approve/reject.
- **Facilitator chat / Q&A**: Questions, replies, announcements in learn view; dedicated **instructor Q&A page**; status (open/resolved); reply **notifications**.
- **My Courses** / **Instructing** / **My learning**: Per-user dashboards. **Learner dashboard**: points, badges, in progress, certificates, recent activity.
- **Responsive Design**: Fully responsive and optimized for all devices—mobile phones, tablets, and desktops. Mobile-first approach with Bootstrap 5.3.2. See [responsive-design.md](docs/responsive-design.md) for details.

## Requirements

- PHP 8.2+
- Composer
- MySQL 5.7+ (or MariaDB) or SQLite
- WAMP (or similar) with Apache pointing to `public/`

## Setup

### 1. Install dependencies

```bash
cd c:\wamp64\www\Training\lms
composer install
```

If Composer fails (e.g. proxy), fix your `composer` config or network, then run `composer install` again.

### 2. Environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env`: set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for MySQL, or use SQLite:

```env
DB_CONNECTION=sqlite
# comment out DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

Create the SQLite file if needed:

```bash
type nul > database\database.sqlite
```

### 3. Database

```bash
php artisan migrate
php artisan db:seed
```

**Module 1 sections (Performance Management course):** To split "Module 1: Understanding SALGA 2026 Context" into individual sections (Introduction, SALGA's Strategic Context, Six Strategic Outcomes, etc.) under MODULE 1:

```bash
php artisan db:seed --class=Module1SectionsSeeder
```

**Day 2 & Day 3 (3-day flow):** To insert "Day 2: Opening & Recap" after Module 4 and "Day 3: Opening & Recap" after Module 8, so the sidebar shows DAY 1 → DAY 2 → DAY 3 → Course Closure:

```bash
php artisan db:seed --class=Day2Day3Seeder
```

**Module Knowledge Checks (pass-to-unlock):** To add a Knowledge Check after each of Modules 1–12. You must score at least 70% to unlock the next module. Run after Module 1 sections and Day 2/Day 3 seeders:

```bash
php artisan db:seed --class=ModuleQuizzesSeeder
```

**Roles (existing installs):** If users have no roles in `model_has_roles`, or you use a legacy `users.role` column, run:

```bash
php artisan db:seed --class=RolesSeeder
```

This ensures `student`, `facilitator`, and `admin` roles exist and (when `users.role` exists) syncs each user’s role into `model_has_roles`.

**32 single-course catalog (locked until enroll):** To add the 32 courses from `single-course-1.html` … `single-course-32.html` (in the `Training` folder) to the student “All Courses” view. They appear as **locked**; only **enrollment** unlocks them:

```bash
php artisan db:seed --class=SingleCourseSeeder
```

**Gamification (points, badges, leaderboard):** To seed default badges (First steps, Knowledge Check master, Course complete):

```bash
php artisan db:seed --class=GamificationSeeder
```

### 4. Run the app

**Option A – PHP built-in server**

```bash
php artisan serve
```

Then open: `http://localhost:8000`

**Option B – WAMP**

- Ensure the project is under `c:\wamp64\www\Training\lms`.
- Point Apache vhost (or `www`) document root to `c:\wamp64\www\Training\lms\public`.
- Use a URL like: `http://localhost/Training/lms/public` (adjust to your WAMP setup).

### 5. Seeded users

| Email               | Password | Role     |
|---------------------|----------|----------|
| admin@lms.test      | password | admin    |
| instructor@lms.test | password | instructor |
| student@lms.test    | password | student  |

## Project structure (vs wplms-example)

| WPLMS (WordPress)     | This Laravel LMS                    |
|-----------------------|-------------------------------------|
| `archive-course.php`  | `CourseController@index`            |
| `course/single/home`  | `CourseController@show` + learn     |
| `course/single/curriculum` | Course show + learn sidebar   |
| `course/course-loop`  | `courses.index` grid                |
| Units / Knowledge Checks | `units` table (type: lesson, quiz, assignment) |
| BuddyPress courses    | `enrollments`, `unit_progress`      |

## Routes

- `GET /` → redirect (admin → admin dashboard; instructor → instructor dashboard; else courses)
- **Auth:** `GET/POST /login`, `GET/POST /register`, `POST /logout`. `GET/POST /forgot-password`, `GET/POST /reset-password/{token}`. `GET/PUT /profile`, `PUT /profile/password`. - **Courses:** `GET /courses` (search `q`, tag, order), `GET /courses/my-courses`, `GET /courses/instructor`, `GET /courses/create`, `POST /courses`, `GET /courses/{course}`, `GET/PUT /courses/{course}/edit`, `DELETE /courses/{course}`, `POST /courses/{course}/enroll`. `GET /courses/{course}/attendance`, `GET /courses/{course}/attendance/export`. `POST /courses/{course}/reviews`, `POST /courses/{course}/rate-facilitator`.
- **Units:** `GET/PUT /courses/{course}/units/{unit}/edit`, `POST /courses/{course}/units/{unit}/refresh-from-file`, `GET/PUT /courses/{course}/units/{unit}/quiz`.
- **Learn:** `GET /learn/{course}`, `POST /learn/{course}/attendance`, `POST /learn/{course}/unit/{unit}/complete`, `POST /learn/{course}/quiz/{unit}`, `POST /learn/{course}/assignment/{unit}/submit`. `GET /learn/{course}/facilitator-chat`, `POST /learn/{course}/facilitator-chat`, `PATCH /learn/{course}/facilitator-chat/{message}`.
- **Instructor:** `GET /instructor`, `GET /instructor/stats`, `GET /instructor/quiz-stats`, `GET /instructor/results`, `GET /instructor/ratings`, `POST /instructor/request-course/{course}`. `GET /instructor/submissions`, `GET /instructor/submissions/{id}/grade`, `PUT /instructor/submissions/{id}`. `GET /instructor/courses/{course}/facilitator-chat`.
- **Admin:** `GET /admin`. Users (`/admin/users`, `put /admin/users/{user}/role`), badges, tags, instructor-requests (approve/reject), facilitator-ratings.
- **Other:** `GET /my-learning`, `GET /leaderboard`, `GET /certificates/{id}`, `GET /certificates/{id}/pdf`. `GET /notifications`, `GET /notifications/{id}/read-and-go`, `POST /notifications/mark-all-read`.

## Licence

MIT.
