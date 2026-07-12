# Phase 2 Step 2.1 — Parallel league memberships (data model)

**Date:** 2026-07-12  
**Status:** Approved — implementing with gap fixes  
**Parent roadmap:** After Phase 1 membership numbers; before admin multi-league UI (2.2) and directory (Phase 3)

## Goal

Store **multiple active wing memberships** per user (`main`, `youth`, `women`, `veterans`) while keeping one shared `users.membership_number`.

## Gap fixes (applied)

| Gap | Fix |
|-----|-----|
| Partial unique “active only” is awkward on MySQL | Unique `(user_id, wing)` always; **reactivate** ended rows instead of inserting duplicates |
| Admin still edits `users.wing` | Sync wing field ↔ `memberships` in Step 2.1 (bridge until 2.2 UI) |
| Invite sets wing before full member | Prefill stays on `users.wing`; membership rows created on **full member** (auto `main` + sync wing if set) |
| Suspended members | Do **not** end memberships; standing already blocks access |
| Course gates use `users.wing` | Keep column; sync **primary wing** after every membership change |
| Naming clash with `MembershipService` (academy) | New `WingMembershipService` |

## Data model

**`memberships`**
- `user_id`, `wing` (`main|youth|women|veterans`)
- `status` (`active|ended`), `joined_at`, `ended_at`, `assigned_by_user_id`
- Unique `(user_id, wing)`

## Rules

1. Full member → `ensureMainMembership` (active `main`).
2. If `users.wing` is a league and user is full member → ensure that wing active too.
3. Primary `users.wing`: prefer current value if still active; else first active non-main (youth→women→veterans); else `main` if active; else null.
4. Ending a league (later UI): set `status=ended`, `ended_at=now()`; never delete.

## Out of scope (2.2+)

Admin multi-select UI, gender/age league rules, course auto-grant, structure offices, directory.
