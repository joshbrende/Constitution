---
name: commands
description: "Skill for the Commands area of constitution. 6 symbols across 1 files."
---

# Commands

6 symbols | 1 files | Cohesion: 83%

## When to Use

- Working with code in `backend/`
- Understanding how handle, parseSections, extractPreamble work
- Modifying commands-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Console/Commands/ImportZimbabweConstitution.php` | handle, parseSections, extractPreamble, cleanBody, __construct (+1) |

## Entry Points

Start here when exploring this area:

- **`handle`** (Method) — `backend/app/Console/Commands/ImportZimbabweConstitution.php:69`
- **`parseSections`** (Method) — `backend/app/Console/Commands/ImportZimbabweConstitution.php:141`
- **`extractPreamble`** (Method) — `backend/app/Console/Commands/ImportZimbabweConstitution.php:209`
- **`cleanBody`** (Method) — `backend/app/Console/Commands/ImportZimbabweConstitution.php:217`
- **`__construct`** (Method) — `backend/app/Console/Commands/ImportZimbabweConstitution.php:45`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `handle` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 69 |
| `parseSections` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 141 |
| `extractPreamble` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 209 |
| `cleanBody` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 217 |
| `__construct` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 45 |
| `buildSectionToChapterMap` | Method | `backend/app/Console/Commands/ImportZimbabweConstitution.php` | 51 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Handle → Disk` | cross_community | 6 |
| `Handle → Path` | cross_community | 6 |
| `Handle → Roles` | cross_community | 5 |
| `Handle → ExtractPreamble` | intra_community | 3 |
| `Handle → CleanBody` | intra_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Admin | 1 calls |
| Feature | 1 calls |

## How to Explore

1. `gitnexus_context({name: "handle"})` — see callers and callees
2. `gitnexus_query({query: "commands"})` — find related execution flows
3. Read key files listed above for implementation details
