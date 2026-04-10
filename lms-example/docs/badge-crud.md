# Badge CRUD (admin)

## Overview

Admins can create and edit badges: name, slug, description, icon, and `points_required`. Badges with `points_required > 0` are auto‑awarded by `GamificationService` when a user’s points reach that value.

---

## Routes

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/admin/badges` | `admin.badges.index` | `BadgeController@index` |
| GET | `/admin/badges/create` | `admin.badges.create` | `BadgeController@create` |
| POST | `/admin/badges` | `admin.badges.store` | `BadgeController@store` |
| GET | `/admin/badges/{badge}/edit` | `admin.badges.edit` | `BadgeController@edit` |
| PUT | `/admin/badges/{badge}` | `admin.badges.update` | `BadgeController@update` |

All require `auth` and `isAdmin()`.

---

## User flow

1. **List** – **Admin → Badges** → table: Name, Slug, Points required, Icon, **Edit**.
2. **Create** – **Create badge** → form → **Create badge**.
3. **Edit** – **Edit** on a row → form → **Update badge**.

---

## Form fields

- **Name** (required): Display name.
- **Slug** (optional): Unique identifier. If blank, generated from name (and uniquified).
- **Description** (optional, max 500): Shown in tooltips etc.
- **Icon** (optional): Bootstrap Icons class (e.g. `bi-trophy`, `bi-star`).
- **Points required** (default 0): When user points ≥ this, `GamificationService::checkBadges()` may attach the badge. Use **0** to disable auto‑award (e.g. for manual or code‑only badges).

---

## Files

| File | Role |
|------|------|
| `app/Http/Controllers/BadgeController.php` | `index`, `create`, `store`, `edit`, `update`, `ensureAdmin`, `uniqueSlug` |
| `resources/views/admin/badges/index.blade.php` | List + Create button |
| `resources/views/admin/badges/create.blade.php` | Create form |
| `resources/views/admin/badges/edit.blade.php` | Edit form |
| `resources/views/admin/badges/_form.blade.php` | Shared form fields |
| `routes/web.php` | `admin.badges.*` |
| `resources/views/layouts/admin.blade.php` | **Badges** in sidebar |

---

## Slug and `GamificationService`

- `ensureBadge(User $user, string $slug)` and `checkBadges()` look up badges by `slug`.  
- Slugs must be unique.  
- Existing code uses e.g. `first-steps`, `quiz-master`, `course-complete`; create matching badges in Admin → Badges if they are missing.
