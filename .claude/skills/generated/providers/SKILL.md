---
name: providers
description: "Skill for the Providers area of constitution. 6 symbols across 3 files."
---

# Providers

6 symbols | 3 files | Cohesion: 86%

## When to Use

- Working with code in `backend/`
- Understanding how boot, registerBladeDirectives, configureRateLimiting work
- Modifying providers-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Providers/AppServiceProvider.php` | boot, registerBladeDirectives, configureRateLimiting, registerDashboardComposers |
| `backend/app/Models/AdminActivityRead.php` | user |
| `backend/app/Http/Controllers/Admin/AdminActivityController.php` | markSeen |

## Entry Points

Start here when exploring this area:

- **`boot`** (Method) — `backend/app/Providers/AppServiceProvider.php:32`
- **`registerBladeDirectives`** (Method) — `backend/app/Providers/AppServiceProvider.php:61`
- **`configureRateLimiting`** (Method) — `backend/app/Providers/AppServiceProvider.php:68`
- **`registerDashboardComposers`** (Method) — `backend/app/Providers/AppServiceProvider.php:105`
- **`user`** (Method) — `backend/app/Models/AdminActivityRead.php:14`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `boot` | Method | `backend/app/Providers/AppServiceProvider.php` | 32 |
| `registerBladeDirectives` | Method | `backend/app/Providers/AppServiceProvider.php` | 61 |
| `configureRateLimiting` | Method | `backend/app/Providers/AppServiceProvider.php` | 68 |
| `registerDashboardComposers` | Method | `backend/app/Providers/AppServiceProvider.php` | 105 |
| `user` | Method | `backend/app/Models/AdminActivityRead.php` | 14 |
| `markSeen` | Method | `backend/app/Http/Controllers/Admin/AdminActivityController.php` | 11 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Boot → Disk` | cross_community | 6 |
| `Boot → Path` | cross_community | 6 |
| `Boot → Roles` | cross_community | 5 |
| `Boot → User` | intra_community | 3 |
| `Boot → CanAccessSection` | cross_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Models | 1 calls |
| Services | 1 calls |

## How to Explore

1. `gitnexus_context({name: "boot"})` — see callers and callees
2. `gitnexus_query({query: "providers"})` — find related execution flows
3. Read key files listed above for implementation details
