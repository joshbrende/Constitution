# Input Sanitization and Validation

This document describes input sanitization and validation measures implemented across the application.

---

## HTML Sanitization

### Output (views)

- **Party Organ body** (`sections/party-organ.blade.php`): Rendered via `HtmlSanitizer::sanitize()` before `{!! !!}` output.
- **Library Document body** (`sections/library-document.blade.php`): Same treatment.

### Input (controllers)

- **Party Organs** (`PartyOrgansController`): Body is sanitized before `create()` and `update()`.
- **Library Documents** (`LibraryController`): Body is sanitized in `validateDocument()` before persistence.

### HtmlSanitizer

Location: `app/Support/HtmlSanitizer.php`

- Uses **mews/purifier** when available for full XSS protection.
- Fallback: `strip_tags()` with safe tag allowlist + regex to neutralize `javascript:`, `data:`, `vbscript:` in `href` attributes.

---

## URL Validation

### SafeUrlRule

Location: `app/Rules/SafeUrlRule.php`

- Ensures URLs use `http` or `https` scheme only.
- Rejects `javascript:`, `data:`, and other non-http(s) schemes.
- Relative URLs (no scheme) are allowed.

### Applied to

- **Home Banners**: `image_url`, `cta_url`
- **Priority Projects**: `image_url`
- **Presidium Members**: `photo_url`

---

## Certificate Verification

- **Controller**: `CertificateVerificationController`
- **Parameters**: `id`, `number`, `code`, `token` are trimmed and length-limited before use:
  - `id`: 36 chars (UUID)
  - `number`: 50 chars
  - `code`: 12 chars
  - `token`: 64 chars

---

## National ID (Zimbabwe)

### ZimbabweNationalIdRule

Location: `app/Rules/ZimbabweNationalIdRule.php`

- Validates Zimbabwe National ID format.
- Pattern: 2 digits + 4–7 digits + 1 letter + 2 digits (separators optional).
- Examples: `08-2047823Q29`, `082047823Q29`.

### Applied to

- **Profile API** (`ProfileController`): `national_id` on `PUT /api/v1/profile`.

---

## Other Validation

- **Static Pages**: `body` max length 50,000 characters.
- **Dialogue messages** (`DialogueController::storeMessage`): `body` max 4000 chars; HTML stripped with `strip_tags()` before storage (plain-text UGC).
- **All user inputs**: Laravel validation rules (type, max, exists, regex, etc.) are applied across controllers.

---

## Mobile app (Expo) — client validation

Defence in depth: the API remains authoritative; the mobile app validates for UX and early rejection.

| Module | Location | Schemas |
|--------|----------|---------|
| Validation | `mobile/src/lib/validation.js` | Zod schemas aligned with Laravel API rules |
| Normalization | `mobile/src/lib/sanitize.js` | Trim, email lowercase, national ID input format |
| Screens | Register, Login, Forgot password, Profile, Chat | `validateForm()` before API calls |

**Dependency:** `zod` (see `mobile/package.json`).

**Not duplicated on mobile:** CORS (handled by Laravel `config/cors.php`); rich HTML Purifier (admin/library only); UK-specific patterns from generic web templates.

**Zimbabwe National ID:** Client mirrors `ZimbabweNationalIdRule` regex; server re-validates on `PUT /api/v1/profile`.

---

## Related

- [CERTIFICATE-SECURITY.md](CERTIFICATE-SECURITY.md) – certificate verification and admin
- [AUDIT-LOGGING.md](AUDIT-LOGGING.md) – audit events for security-sensitive actions
