# Continue LMS Setup (Existing `lms` Database)

The Laravel LMS has been adapted to use your existing **`lms`** database (MySQL, user `root`, no password) and its tables.

## 1. Environment

- **`.env`** uses `DB_DATABASE=lms`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty).
- **`APP_KEY`** is set. If you use a fresh `.env`, run `php artisan key:generate` (or copy from `.env.example` and generate).

## 2. Create `unit_completions` Table

The app uses a `unit_completions` table to track which units each user has completed. It is not part of your current 30 tables.

**Option A – Artisan (if bootstrap/cache is writable)**

```bash
cd c:\wamp64\www\Training\lms
php artisan migrate --path=database/migrations/2025_01_26_200000_create_unit_completions_table.php --force
```

**Option B – Standalone script (if Artisan fails)**

```bash
cd c:\wamp64\www\Training\lms
php create_unit_completions_table.php
```

This creates `unit_completions` (MyISAM, no FKs) to match your existing DB. **Already run** during setup.

## 3. Run the App

**PHP built-in server:**

```bash
cd c:\wamp64\www\Training\lms
php artisan serve
```

Then open: **http://localhost:8000**

**WAMP:**

Point your vhost or `DocumentRoot` to `c:\wamp64\www\Training\lms\public` and use a URL like:

**http://localhost/Training/lms/public**

## 4. Schema Mapping

| Your DB              | Laravel LMS usage                                      |
|----------------------|--------------------------------------------------------|
| `users`              | Auth; roles via `model_has_roles` + `roles`           |
| `courses`            | `status='published'`, `enrollment_count`, `featured_image`, `short_description` |
| `units`              | Curriculum; `course_id`, `order`, `unit_type`         |
| `enrollments`        | `progress_percentage`, `progress_status`, `enrolled_at` |
| `course_progress`    | Overall progress (units_completed, overall_progress)   |
| `unit_completions`   | **New** – per-unit completion (run migration or script above) |

## 5. Roles (Spatie-style)

Instructor/admin checks use `model_has_roles` and `roles`. The app looks for role names such as `admin`, `instructor`, `teacher`, etc. Ensure your existing users have the correct role assignments in `model_has_roles`.

## 6. Notes

- **Quizzes / assignments:** Units with `unit_type = 'quiz'` or `'assignment'` are listed in the curriculum but cannot be completed in-app yet; only lesson-type units (e.g. `text`, `video`) support “Mark complete”.
- **Old migrations:** The `database/migrations` folder still contains migrations for users, courses, etc. **Do not run `php artisan migrate`** without `--path`; those migrations target a different schema. Use only the `unit_completions` migration (or the PHP script) as above.

## 7. Troubleshooting

- **Bootstrap cache:** If `php artisan` fails with “Access is denied” on `bootstrap/cache`, fix permissions for that directory or run Artisan from a terminal outside the sandbox.
- **DB connection:** Confirm MySQL is running, and that `lms` exists with user `root` and no password.
- **Unit completions:** If “Mark complete” errors, ensure `unit_completions` exists (run the migration or `create_unit_completions_table.php`).
