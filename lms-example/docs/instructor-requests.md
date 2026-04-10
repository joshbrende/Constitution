# Instructor requests

Facilitators can request to instruct a course when it has no assigned instructor (`instructor_id` null). Admins approve or reject; on approve, `course.instructor_id` is set and other pending requests for that course are auto-rejected.

## Model and migration

**`instructor_requests`**: `id`, `course_id`, `user_id` (facilitator), `status` (`pending`|`approved`|`rejected`), `decided_at`, `decided_by` (FK users), `admin_notes`, `timestamps`. FKs with `cascadeOnDelete` on course/user.

**`InstructorRequest`**: `isPending()`, `isApproved()`, `isRejected()`; `course()`, `user()`, `decidedBy()`.

## Routes

| Method | URI | Name | Who |
|--------|-----|------|-----|
| POST | `/instructor/request-course/{course}` | `instructor.request-course` | Facilitator (not admin) |
| GET | `/admin/instructor-requests` | `admin.instructor-requests.index` | Admin |
| POST | `/admin/instructor-requests/{instructorRequest}/approve` | `admin.instructor-requests.approve` | Admin |
| POST | `/admin/instructor-requests/{instructorRequest}/reject` | `admin.instructor-requests.reject` | Admin |

## Facilitator: request to instruct

- **When:** Course `instructor_id` is null. Only facilitators (not admins) can request; admins assign via course edit.
- **Guard:** `canEditCourses()` and `!isAdmin()`. If course already has instructor → redirect with message. If same user already has `status=pending` for this course → redirect.
- **Action:** `InstructorRequest::create(['course_id','user_id','status'=>'pending'])`. Redirect to instructor dashboard with success message.

## Admin: list

- `InstructorRequest::with(['course','user','decidedBy'])->latest()->paginate(20)`. `$pendingCount` for badge/link. View: `admin.instructor-requests`.

## Admin: approve

- Request must be `isPending()`. If already processed → redirect with message. If `course.instructor_id` is already set → mark request rejected with `admin_notes` "Course already has a facilitator", redirect.
- In a DB transaction: (1) `course.update(['instructor_id' => request.user_id])`, (2) request `status=approved`, `decided_at`, `decided_by`, (3) other `InstructorRequest` for same course with `status=pending` → `rejected`, `decided_at`, `decided_by`, `admin_notes` "Another facilitator was approved for this course."

## Admin: reject

- Request must be `isPending()`. `status=rejected`, `decided_at`, `decided_by`, `admin_notes` from request (optional).

## UI

- **Instructor dashboard:** For courses with `instructor_id` null, "Request to instruct" (or similar) → `instructor.request-course`.
- **Admin:** "Instructor requests" in nav → list; Approve / Reject; reject can have optional notes.
