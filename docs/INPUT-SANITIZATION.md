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
- **All user inputs**: Laravel validation rules (type, max, exists, regex, etc.) are applied across controllers.

---

## Related

- [CERTIFICATE-SECURITY.md](CERTIFICATE-SECURITY.md) – certificate verification and admin
- [AUDIT-LOGGING.md](AUDIT-LOGGING.md) – audit events for security-sensitive actions
