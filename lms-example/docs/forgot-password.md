# Forgot password

## Overview

Users can request a password reset link by email. The link expires after 60 minutes (configurable). After resetting, they are redirected to the login page.

---

## Routes

| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/forgot-password` | `password.request` | `AuthController@showForgotPassword` |
| POST | `/forgot-password` | `password.email` | `AuthController@sendResetLink` |
| GET | `/reset-password/{token}` | `password.reset` | `AuthController@showResetPassword` |
| POST | `/reset-password` | `password.update` | `AuthController@resetPassword` |

---

## User flow

1. **Request link**  
   User goes to **Login** → **Forgot password?** (or `/forgot-password`), enters email, submits.  
   - If the email exists: success message; an email is sent with a link to `/reset-password/{token}?email=...`.  
   - If the email does not exist: same success message (no user enumeration).  
   - Throttled: after 60 requests per minute, an error is shown (config: `config/auth.php` → `passwords.users.throttle`).

2. **Reset password**  
   User opens the link in the email, lands on `/reset-password/{token}?email=...`, enters new password and confirmation, submits.  
   - If the token is valid and not expired: password is updated, redirect to **Login** with success.  
   - If the token is invalid or expired: error on the form.

3. **Login**  
   User signs in with the new password.

---

## Files touched

| File | Role |
|------|------|
| `app/Models/User.php` | `getEmailForPasswordReset()`, `sendPasswordResetNotification()` |
| `app/Http/Controllers/AuthController.php` | `showForgotPassword`, `sendResetLink`, `showResetPassword`, `resetPassword` |
| `routes/web.php` | 4 routes for request link, send link, show reset form, perform reset |
| `resources/views/auth/forgot-password.blade.php` | Form: email → send reset link |
| `resources/views/auth/reset-password.blade.php` | Form: token (hidden), email, password, password_confirmation |
| `resources/views/auth/login.blade.php` | “Forgot password?” link |
| `resources/views/layouts/app.blade.php` | `session('status')` for success messages |

---

## Configuration

### Auth (`config/auth.php`)

- `passwords.users.table` = `password_reset_tokens`  
- `passwords.users.expire` = `60` (minutes)  
- `passwords.users.throttle` = `60` (seconds to wait after throttle)

### Mail

The reset link is sent with Laravel’s `Illuminate\Auth\Notifications\ResetPassword` notification.

- **Mail driver** must be set in `.env` (e.g. `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`).
- **`APP_URL`** must match the app’s real URL; the reset link is built from it.
- For local/dev without SMTP, use `MAIL_MAILER=log` and check `storage/logs/laravel.log` for the reset URL.

### Translations

Laravel’s `passwords` strings (e.g. `passwords.sent`, `passwords.reset`, `passwords.throttled`, `passwords.user`) come from `vendor/laravel/framework/.../lang/en/passwords.php`. To override, publish and edit `lang/en/passwords.php` (or your locale).

---

## Database

Uses the existing `password_reset_tokens` table:

- `email` (primary)
- `token`
- `created_at`

Laravel hashes the token in the DB; the value in the URL is the plain token passed to the notification.

---

## Security

- Same message for existing and non‑existing email on “send link” (no email enumeration).
- Token is single‑use and time‑limited.
- New password must meet `Password::defaults()` (see `Password` in `AuthController`).
- `remember_token` is regenerated on reset.

---

## Testing (manual)

1. Ensure `MAIL_MAILER=log` and `APP_URL` is correct.
2. Open `/forgot-password`, submit an existing user email.
3. In `storage/logs/laravel.log`, find the reset URL and open it.
4. Set a new password and submit; then log in with that password.
5. For an unknown email, the same success message is shown and no email is sent.
6. Use an old or altered token: reset form should show an error.
