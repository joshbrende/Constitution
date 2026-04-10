---
name: unit
description: "Skill for the Unit area of constitution. 5 symbols across 3 files."
---

# Unit

5 symbols | 3 files | Cohesion: 100%

## When to Use

- Working with code in `lms-example/`
- Understanding how AssignmentGradedNotification, AssignmentSubmission, test_implements_should_queue work
- Modifying unit-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `lms-example/tests/Unit/AssignmentGradedNotificationTest.php` | test_implements_should_queue, test_via_returns_database_and_mail |
| `lms-example/app/Notifications/AssignmentGradedNotification.php` | AssignmentGradedNotification, via |
| `lms-example/app/Models/AssignmentSubmission.php` | AssignmentSubmission |

## Entry Points

Start here when exploring this area:

- **`AssignmentGradedNotification`** (Class) — `lms-example/app/Notifications/AssignmentGradedNotification.php:10`
- **`AssignmentSubmission`** (Class) — `lms-example/app/Models/AssignmentSubmission.php:7`
- **`test_implements_should_queue`** (Method) — `lms-example/tests/Unit/AssignmentGradedNotificationTest.php:10`
- **`test_via_returns_database_and_mail`** (Method) — `lms-example/tests/Unit/AssignmentGradedNotificationTest.php:17`
- **`via`** (Method) — `lms-example/app/Notifications/AssignmentGradedNotification.php:18`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `AssignmentGradedNotification` | Class | `lms-example/app/Notifications/AssignmentGradedNotification.php` | 10 |
| `AssignmentSubmission` | Class | `lms-example/app/Models/AssignmentSubmission.php` | 7 |
| `test_implements_should_queue` | Method | `lms-example/tests/Unit/AssignmentGradedNotificationTest.php` | 10 |
| `test_via_returns_database_and_mail` | Method | `lms-example/tests/Unit/AssignmentGradedNotificationTest.php` | 17 |
| `via` | Method | `lms-example/app/Notifications/AssignmentGradedNotification.php` | 18 |

## How to Explore

1. `gitnexus_context({name: "AssignmentGradedNotification"})` — see callers and callees
2. `gitnexus_query({query: "unit"})` — find related execution flows
3. Read key files listed above for implementation details
