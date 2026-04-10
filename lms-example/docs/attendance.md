# Attendance register

Enrolled learners can register attendance on **Day 1: Opening & Course Overview** (or similarly titled) units. The form is shown in the learn view when the current unit matches `isDay1OpeningUnit()`. Admins view the register per course and can **export CSV** (see [export-attendance.md](export-attendance.md)).

## When the form is shown

- **`LearnController::show`:** If the current unit exists and `isDay1OpeningUnit($unit)` is true, `$showAttendanceRegister = true`. `$attendanceSubmitted` is true when an `AttendanceRegister` row exists for `(enrollment_id, unit_id)`.
- **`isDay1OpeningUnit(Unit $unit)`:** Title matches `/^Day\s*1\s*:?\s*Opening.*Course\s*Over/i` or contains `Day 1: Opening` (case-insensitive). Other units do not show the register.

## Storing attendance

**Route:** `POST /learn/{course}/attendance` → `learn.attendance.store` (`LearnController@storeAttendance`).

- **Guard:** User enrolled in course.
- **Validation:** `unit_id` (exists), `title` (nullable, max 50), `name`, `surname`, `designation`, `organisation`, `contact_number`, `email`. `unit` must belong to course and `isDay1OpeningUnit($unit)`; else 400.
- **Action:** `AttendanceRegister::updateOrCreate` on `['enrollment_id','unit_id']` with `course_id`, `user_id`, and the validated fields. Redirect to learn with `unit` and success message.

## Migration and model

**`attendance_registers`**: `id`, `course_id`, `unit_id`, `user_id`, `enrollment_id`, `title`, `name`, `surname`, `designation`, `organisation`, `contact_number`, `email`, `timestamps`. Unique `(enrollment_id, unit_id)`; indexes on `course_id`, `unit_id`.

## Admin: view and export

- **View:** `GET /courses/{course}/attendance` → `courses.attendance` (admin only). `AttendanceRegister::where('course_id')` with `user`, `unit`; one row per registration.
- **Export:** `GET /courses/{course}/attendance/export` → `courses.attendance.export` (admin only). UTF-8 BOM CSV; see [export-attendance.md](export-attendance.md).

## UI

- **Learn:** When `$showAttendanceRegister` and not `$attendanceSubmitted`, render form (title, name, surname, designation, organisation, contact_number, email) posting to `learn.attendance.store` with `unit_id`. After submit, form is hidden.
- **Admin:** From admin or course context, open a course’s attendance register; **Export CSV** when `$rows->isNotEmpty()`.
