# User management (admin)

## Overview

Admins can list users, view a user’s detail (enrollments, role), and change a user’s role to Student, Facilitator, or Admin.

---

## Routes

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/admin/users` | `admin.users.index` | `UserController@index` |
| GET | `/admin/users/{user}` | `admin.users.show` | `UserController@show` |
| PUT | `/admin/users/{user}/role` | `admin.users.update-role` | `UserController@updateRole` |

All require `auth`; each action also enforces `isAdmin()`.

---

## User flow

1. **List users**  
   **Admin → Users** (sidebar or dashboard) → `GET /admin/users`.  
   Paginated table: Name, Email, Role, **View**.

2. **View user**  
   **View** on a row → `GET /admin/users/{user}`.  
   - **Account:** email, points, current role.  
   - **Set role:** dropdown (Student / Facilitator / Admin) and **Update role** → `PUT /admin/users/{user}/role`.  
   - **Enrollments:** course title, progress %; link to course.

3. **Update role**  
   Choose role, **Update role** → `PUT /admin/users/{user}/role` with `role=student|facilitator|admin`.  
   User’s roles are replaced with the selected one. Redirect back to user show with *Role updated to X.*

---

## Files

| File | Role |
|------|------|
| `app/Http/Controllers/UserController.php` | `index`, `show`, `updateRole`; `ensureAdmin()` |
| `resources/views/admin/users/index.blade.php` | Users table, pagination |
| `resources/views/admin/users/show.blade.php` | Account, role form, enrollments |
| `routes/web.php` | `admin.users.index`, `admin.users.show`, `admin.users.update-role` |
| `resources/views/layouts/admin.blade.php` | **Users** in sidebar |
| `resources/views/admin/dashboard.blade.php` | Users card “View all”, **Users** quick action |

---

## Access and roles

- **`UserController`**  
  Each method calls `ensureAdmin()`; non-admins get 403.

- **Roles**  
  Stored in `roles` and `model_has_roles`. `updateRole` syncs a single role: `$user->roles()->sync([$role->id])`. Allowed: `student`, `facilitator`, `admin`.

---

## Validation

- **updateRole**  
  `role`: required, `in:student,facilitator,admin`.
