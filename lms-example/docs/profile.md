# User profile

## Overview

Authenticated users can view and edit their profile: name, surname, email, and change their password. The profile page also shows points and badges.

---

## Routes

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/profile` | `profile.edit` | `ProfileController@edit` |
| PUT | `/profile` | `profile.update` | `ProfileController@update` |
| PUT | `/profile/password` | `profile.password` | `ProfileController@updatePassword` |

All require `auth` middleware.

---

## User flow

1. **Open profile**  
   Click the user’s name in the nav (app, facilitator, or learn layout) → `GET /profile`.

2. **Update account details**  
   Edit name, surname, email → **Save changes** → `PUT /profile`.  
   - `name` required; `surname` optional; `email` required and unique (excluding current user).  
   - Redirect back to profile with *Profile updated.*

3. **Change password**  
   Enter current password, new password, confirm → **Change password** → `PUT /profile/password`.  
   - `current_password` must match; `password` must match `Password::defaults()`.  
   - Redirect back to profile with *Password changed.*

---

## Files

| File | Role |
|------|------|
| `app/Http/Controllers/ProfileController.php` | `edit`, `update`, `updatePassword` |
| `resources/views/profile/edit.blade.php` | Profile form, change-password form, points & badges sidebar |
| `routes/web.php` | `profile.edit`, `profile.update`, `profile.password` |
| `resources/views/layouts/app.blade.php` | User name → link to `profile.edit` |
| `resources/views/layouts/facilitator.blade.php` | User name → link to `profile.edit` |
| `resources/views/layouts/learn.blade.php` | User name → link to `profile.edit` |

---

## Profile page structure

- **Account details (card)**  
  Form: name, surname, email. `PUT /profile`.

- **Change password (card)**  
  Form: current password, new password, confirm. `PUT /profile/password`.

- **Sidebar (card)**  
  Points (with link to leaderboard), badges (if any).

---

## Validation

- **update**  
  - `name`: required, string, max 255  
  - `surname`: nullable, string, max 255  
  - `email`: required, email, max 255, unique in `users` ignoring current user

- **updatePassword**  
  - `current_password`: required, `current_password` (Laravel: matches `Auth::user()->password`)  
  - `password`: required, confirmed, `Password::defaults()`

---

## Security

- All routes are behind `auth`.
- `current_password` ensures the user knows the existing password before changing it.
- `Password::defaults()` applies the app’s password rules (length, etc.).
