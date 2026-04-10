---
name: support
description: "Skill for the Support area of constitution. 6 symbols across 3 files."
---

# Support

6 symbols | 3 files | Cohesion: 90%

## When to Use

- Working with code in `backend/`
- Understanding how sanitize, fallbackSanitize, store work
- Modifying support-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Support/HtmlSanitizer.php` | sanitize, fallbackSanitize |
| `backend/app/Http/Controllers/Admin/PartyOrgansController.php` | store, update |
| `backend/app/Support/OpsAlerts.php` | notifyPlainText, queueHealthDegraded |

## Entry Points

Start here when exploring this area:

- **`sanitize`** (Method) — `backend/app/Support/HtmlSanitizer.php:10`
- **`fallbackSanitize`** (Method) — `backend/app/Support/HtmlSanitizer.php:26`
- **`store`** (Method) — `backend/app/Http/Controllers/Admin/PartyOrgansController.php:25`
- **`update`** (Method) — `backend/app/Http/Controllers/Admin/PartyOrgansController.php:50`
- **`notifyPlainText`** (Method) — `backend/app/Support/OpsAlerts.php:14`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `sanitize` | Method | `backend/app/Support/HtmlSanitizer.php` | 10 |
| `fallbackSanitize` | Method | `backend/app/Support/HtmlSanitizer.php` | 26 |
| `store` | Method | `backend/app/Http/Controllers/Admin/PartyOrgansController.php` | 25 |
| `update` | Method | `backend/app/Http/Controllers/Admin/PartyOrgansController.php` | 50 |
| `notifyPlainText` | Method | `backend/app/Support/OpsAlerts.php` | 14 |
| `queueHealthDegraded` | Method | `backend/app/Support/OpsAlerts.php` | 37 |

## How to Explore

1. `gitnexus_context({name: "sanitize"})` — see callers and callees
2. `gitnexus_query({query: "support"})` — find related execution flows
3. Read key files listed above for implementation details
