# Phase 2 Step 2.4 — League course auto-grant

**Date:** 2026-07-12  
**Status:** Done — implemented  
**Parent roadmap:** After Step 2.3 (course access uses all active memberships); before Phase 3 member directory  
**Approach:** Map `course.audience` → wing grant (no new column)

## Goal

Let paid, branch-admitted members **enrol in Youth / Women's / Veterans academy pathways without already holding that wing**, and **activate the matching `memberships` row when the league certificate is issued** (Presidium approve).

## Decisions (locked)

| Topic | Choice |
|-------|--------|
| Next step | Auto-grant wing from league pathway (not directory) |
| Grant timing | On **certificate issued** (`presidiumApprove`) |
| Enrolment gate | Open to paid member + branch admission; **no prior wing required** |
| Gender/age rules | Deferred |
| How wing is known | `course.audience` ∈ `youth\|women\|veterans` |

## Enrolment (`CourseAccessService`)

For courses with `audience` in `youth|women|veterans`:

1. Keep `requires_membership` / membership-course completion checks.
2. Keep branch-admission gate when `ACADEMY_REQUIRE_BRANCH_ADMISSION` is true.
3. **Change:** `userMatchesAudience` returns **true** for these league audiences when the user has **member privileges** (`MembershipStandingService::hasMemberPrivileges`), instead of requiring an active wing row or legacy `users.wing`.
4. Non-league audiences unchanged (`all`, `member`, `presidium`, `main` if used).

Rationale: `audience` on league pathways becomes **which wing the course grants**, not **who may start**.

## Grant trigger

In `CertificateApplicationService::presidiumApprove`, after `markFullMember`:

1. Resolve `$wing = strtolower(trim($application->course->audience))`.
2. If `$wing` ∈ `youth|women|veterans`, call `WingMembershipService::ensureActive($user, $wing)`.
3. Call `syncPrimaryWingColumn($user)` so primary `users.wing` follows existing priority rules (do **not** force primary to the newly granted league if the current primary is still active).
4. Extend audit metadata on the existing activate log (or companion log) with `source: league_certificate`, `course_id`, `certificate_application_id`.

Idempotent: `ensureActive` reactivates ended rows; safe if admin already assigned the wing.

## Explicitly out of scope

- Gender/age eligibility fields and gates
- New `courses.grants_wing` column / admin UI
- Grant on exam pass or payment confirmed (only certificate issued)
- Changing when full party membership / membership number is assigned
- Phase 3 member directory

## Tests

1. Member with privileges + branch admission, primary/only `main` (no youth row) **can enrol** in youth league course.
2. Same user without branch admission **cannot enrol** (when config requires it).
3. Presidium approve of youth certificate application → active `youth` membership; `main` still present for full members.
4. Prior wing-match / multi-league access tests remain green where still applicable.

## Docs

Update `docs/MEMBERSHIP-STRATEGY.md`: Step 2.4 done; note league `audience` = pathway/grant target; enrolment open after membership course + branch admission.
