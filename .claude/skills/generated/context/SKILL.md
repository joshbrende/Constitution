---
name: context
description: "Skill for the Context area of constitution. 5 symbols across 5 files."
---

# Context

5 symbols | 5 files | Cohesion: 80%

## When to Use

- Working with code in `backend/`
- Understanding how ReaderDataProvider, set, handle work
- Modifying context-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `mobile/src/context/ReaderDataContext.js` | ReaderDataProvider |
| `backend/app/Models/SiteSetting.php` | set |
| `backend/app/Http/Middleware/RequestContextMiddleware.php` | handle |
| `backend/app/Http/Controllers/SetupWizardController.php` | store |
| `backend/app/Http/Controllers/Admin/AdminPlatformSettingsController.php` | update |

## Entry Points

Start here when exploring this area:

- **`ReaderDataProvider`** (Function) — `mobile/src/context/ReaderDataContext.js:10`
- **`set`** (Method) — `backend/app/Models/SiteSetting.php:30`
- **`handle`** (Method) — `backend/app/Http/Middleware/RequestContextMiddleware.php:12`
- **`store`** (Method) — `backend/app/Http/Controllers/SetupWizardController.php:53`
- **`update`** (Method) — `backend/app/Http/Controllers/Admin/AdminPlatformSettingsController.php:33`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `ReaderDataProvider` | Function | `mobile/src/context/ReaderDataContext.js` | 10 |
| `set` | Method | `backend/app/Models/SiteSetting.php` | 30 |
| `handle` | Method | `backend/app/Http/Middleware/RequestContextMiddleware.php` | 12 |
| `store` | Method | `backend/app/Http/Controllers/SetupWizardController.php` | 53 |
| `update` | Method | `backend/app/Http/Controllers/Admin/AdminPlatformSettingsController.php` | 33 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 1 calls |
| Services | 1 calls |

## How to Explore

1. `gitnexus_context({name: "ReaderDataProvider"})` — see callers and callees
2. `gitnexus_query({query: "context"})` — find related execution flows
3. Read key files listed above for implementation details
