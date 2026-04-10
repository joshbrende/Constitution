---
name: observers
description: "Skill for the Observers area of constitution. 7 symbols across 2 files."
---

# Observers

7 symbols | 2 files | Cohesion: 92%

## When to Use

- Working with code in `lms-example/`
- Understanding how invalidateTagsCache, created, updated work
- Modifying observers-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `lms-example/app/Observers/TagObserver.php` | invalidateTagsCache, created, updated, deleted, restored (+1) |
| `backend/app/Models/Course.php` | booted |

## Entry Points

Start here when exploring this area:

- **`invalidateTagsCache`** (Method) — `lms-example/app/Observers/TagObserver.php:12`
- **`created`** (Method) — `lms-example/app/Observers/TagObserver.php:17`
- **`updated`** (Method) — `lms-example/app/Observers/TagObserver.php:22`
- **`deleted`** (Method) — `lms-example/app/Observers/TagObserver.php:27`
- **`restored`** (Method) — `lms-example/app/Observers/TagObserver.php:32`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `invalidateTagsCache` | Method | `lms-example/app/Observers/TagObserver.php` | 12 |
| `created` | Method | `lms-example/app/Observers/TagObserver.php` | 17 |
| `updated` | Method | `lms-example/app/Observers/TagObserver.php` | 22 |
| `deleted` | Method | `lms-example/app/Observers/TagObserver.php` | 27 |
| `restored` | Method | `lms-example/app/Observers/TagObserver.php` | 32 |
| `forceDeleted` | Method | `lms-example/app/Observers/TagObserver.php` | 37 |
| `booted` | Method | `backend/app/Models/Course.php` | 14 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Controllers | 1 calls |

## How to Explore

1. `gitnexus_context({name: "invalidateTagsCache"})` — see callers and callees
2. `gitnexus_query({query: "observers"})` — find related execution flows
3. Read key files listed above for implementation details
