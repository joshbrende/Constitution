# Restrict register: students only

## Overview

Public registration only creates **Student** accounts. Facilitator and Admin roles are assigned by an administrator via **Admin → Users**.

---

## Changes

### Register form (`resources/views/auth/register.blade.php`)

- **Removed:** "Register as" dropdown (student / facilitator / admin).
- **Added:** Short note: *You will register as a **Student**. Facilitator and Admin roles are assigned by an administrator.*

### AuthController (`app/Http/Controllers/AuthController.php`)

- **`register()`**
  - Validation: no `role` field.
  - Role: always `student` via `Role::firstOrCreate(['name' => 'student', ...])` and `$user->roles()->sync([$role->id])`.
  - Redirect: always `route('courses.index')` (new users are students).

---

## Assigning facilitator or admin

Use **Admin → Users** (or **Admin → Dashboard → Users → View all**):

1. Open a user’s **View** page.
2. In **Set role**, choose **Facilitator** or **Admin**.
3. Click **Update role**.

See [user-management.md](user-management.md).
