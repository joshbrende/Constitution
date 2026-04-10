---
name: policies
description: "Skill for the Policies area of constitution. 9 symbols across 5 files."
---

# Policies

9 symbols | 5 files | Cohesion: 94%

## When to Use

- Working with code in `backend/`
- Understanding how generate, download, allowsPdfActions work
- Modifying policies-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Policies/CertificatePolicy.php` | generate, download, allowsPdfActions |
| `backend/app/Models/Certificate.php` | isRevoked, isExpired, verificationStatus |
| `backend/app/Policies/DialogueThreadPolicy.php` | reply |
| `backend/app/Policies/DialogueChannelPolicy.php` | createThread |
| `backend/app/Models/DialogueChannel.php` | canUserPost |

## Entry Points

Start here when exploring this area:

- **`generate`** (Method) — `backend/app/Policies/CertificatePolicy.php:10`
- **`download`** (Method) — `backend/app/Policies/CertificatePolicy.php:15`
- **`allowsPdfActions`** (Method) — `backend/app/Policies/CertificatePolicy.php:20`
- **`isRevoked`** (Method) — `backend/app/Models/Certificate.php:69`
- **`isExpired`** (Method) — `backend/app/Models/Certificate.php:74`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `generate` | Method | `backend/app/Policies/CertificatePolicy.php` | 10 |
| `download` | Method | `backend/app/Policies/CertificatePolicy.php` | 15 |
| `allowsPdfActions` | Method | `backend/app/Policies/CertificatePolicy.php` | 20 |
| `isRevoked` | Method | `backend/app/Models/Certificate.php` | 69 |
| `isExpired` | Method | `backend/app/Models/Certificate.php` | 74 |
| `verificationStatus` | Method | `backend/app/Models/Certificate.php` | 84 |
| `reply` | Method | `backend/app/Policies/DialogueThreadPolicy.php` | 12 |
| `createThread` | Method | `backend/app/Policies/DialogueChannelPolicy.php` | 9 |
| `canUserPost` | Method | `backend/app/Models/DialogueChannel.php` | 37 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Show → IsRevoked` | cross_community | 3 |
| `Show → IsExpired` | cross_community | 3 |

## How to Explore

1. `gitnexus_context({name: "generate"})` — see callers and callees
2. `gitnexus_query({query: "policies"})` — find related execution flows
3. Read key files listed above for implementation details
