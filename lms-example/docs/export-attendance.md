# Export attendance

## Overview

Admins can export a course’s attendance register as a CSV file from the attendance view. The file uses UTF-8 with BOM so Excel opens it with correct encoding.

---

## Route

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/courses/{course}/attendance/export` | `courses.attendance.export` | `CourseController@exportAttendance` |

Requires `auth`; admins only (`isAdmin()`).

---

## User flow

1. Go to **Admin → Attendance** (or via course) → open a course’s attendance register.
2. If there is at least one record, an **Export CSV** button is shown.
3. Click **Export CSV** → download of `attendance-{course-slug}-{Y-m-d}.csv`.

---

## CSV format

- **Encoding:** UTF-8 with BOM.
- **Delimiter:** comma (`,`).
- **Header row:** `#, Title, Name, Surname, Designation, Organisation, Contact number, Email, Registered at`.
- **Data:** One row per attendance record. `Registered at` is `Y-m-d H:i`. Empty values are exported as empty (no "—").

---

## Files

| File | Role |
|------|------|
| `app/Http/Controllers/CourseController.php` | `exportAttendance()` – `streamDownload` with CSV |
| `resources/views/courses/attendance.blade.php` | **Export CSV** button (only when `$rows->isNotEmpty()`) |
| `routes/web.php` | `courses.attendance.export` |

---

## Access

- `exportAttendance()` calls `isAdmin()`; non-admins receive 403.
