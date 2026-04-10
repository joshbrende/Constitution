# Facilitator ratings

Enrolled users who have **completed** a course (progress 100%) can rate the course’s facilitator once per enrollment. Rating is 1–5 plus optional review text. Facilitators (and admins who instruct) see their own ratings; admins see all.

## Model and migration

**`facilitator_ratings`**: `id`, `enrollment_id` (unique), `instructor_id`, `rating` (1–5), `review` (nullable), `timestamps`. One rating per enrollment.

**`FacilitatorRating`**: `enrollment()`, `instructor()`.

## Routes

| Method | URI | Name | Who |
|--------|-----|------|-----|
| POST | `/courses/{course}/rate-facilitator` | `courses.rate-facilitator` | Enrolled, course complete |
| GET | `/instructor/ratings` | `instructor.ratings` | Facilitator / admin (own) |
| GET | `/admin/facilitator-ratings` | `admin.facilitator-ratings.index` | Admin (all) |

## Submitting a rating (`storeFacilitatorRating`)

- **Conditions:** Logged in; enrolled; `progress_percentage >= 100`; course has `instructor_id`; no existing `FacilitatorRating` for this `enrollment_id`.
- **Validation:** `rating` required 1–5, `review` nullable max 2000.
- **Action:** `FacilitatorRating::create(['enrollment_id','instructor_id'=>course.instructor_id,'rating','review'])`. Redirect to course show with thank-you message.

## Facilitator view (`instructor.ratings`)

- **Guard:** `canEditCourses()`. `FacilitatorRating::where('instructor_id', user.id)` with `enrollment.user`, `enrollment.course`; `latest()->paginate(20)`. `avg(rating)` and `count` for summary.

## Admin view (`admin.facilitator-ratings`)

- **Guard:** `isAdmin()`. `byFacilitator`: `instructor_id`, `AVG(rating)`, `COUNT(*)` grouped, with `instructor`; `ratings` paginated with `enrollment`, `course`, `instructor`.

## UI

- **Course show:** After completion, if not yet rated and course has facilitator: form (1–5 stars, optional review) → `courses.rate-facilitator`.
- **Instructor nav:** "Ratings" → `instructor.ratings`.
- **Admin nav:** "Facilitator ratings" → `admin.facilitator-ratings`.
