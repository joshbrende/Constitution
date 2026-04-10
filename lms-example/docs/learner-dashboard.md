# Learner dashboard (My learning)

## Overview

**My learning** (`/my-learning`) is a dashboard for learners: points, badges, in‑progress and completed courses, certificates, and recent unit completions. Available to all logged‑in users (including facilitators and admins enrolled in courses).

---

## Route

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/my-learning` | `learner.dashboard` | `LearnerController@dashboard` |

Requires `auth`.

---

## Dashboard sections

### Summary cards (top row)

- **Points** – User’s points; link to Leaderboard.
- **Badges** – Count and up to 3 badge names.
- **In progress** – Count of enrollments with progress &lt; 100%; link to My courses.
- **Certificates** – Count of certificates; if &gt; 0, link to the most recent certificate.

### In progress (list)

- Up to 6 enrollments with progress &lt; 100%.
- Each: course title, progress %, link to **Continue** (learn flow).
- Link to **My courses**. Empty: “Browse courses” CTA.

### Certificates (list)

- Up to 6 certificates with course title; link to certificate view.
- Link to **My courses** when there is at least one. Empty: “Complete a course to earn one.”

### Recent activity (optional)

- Shown only if there are unit completions.
- Last 5 unit completions: unit title, course title, completion date/time.

### Footer

- **Browse courses**, **My courses**.

---

## Files

| File | Role |
|------|------|
| `app/Http/Controllers/LearnerController.php` | `dashboard()` – enrollments, certificates, unit completions, counts |
| `resources/views/learner/dashboard.blade.php` | Dashboard layout and sections |
| `routes/web.php` | `learner.dashboard` |
| `resources/views/layouts/app.blade.php` | **My learning** in nav |
| `resources/views/layouts/facilitator.blade.php` | **My learning** in nav |

---

## Data

- **Enrollments** – `Enrollment::where('user_id', $user->id)->with('course')`, ordered by `enrolled_at` desc.
- **In progress** – `progress_percentage < 100`, top 6; full count for the summary card.
- **Certificates** – `Certificate::where('user_id', $user->id)->with('course')->latest('issued_at')->take(6)`; separate count for the card.
- **Recent completions** – `UnitCompletion::where('user_id', $user->id)->with(['course','unit'])->latest('completed_at')->take(5)`.
