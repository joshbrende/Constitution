# Phase 3 — Member directory (member-to-member discovery)

**Date:** 2026-07-12  
**Status:** Done — implemented   
**Parent roadmap:** After Phase 2 wing memberships / league auto-grant

## Goal

Full members can **search a national directory** of other full members from PWA and mobile, via a dedicated API that exposes **minimal** identity fields only.

## Decisions (locked)

| Topic | Choice |
|-------|--------|
| Who may use it | Full members only (`membership_standing = member`) |
| Scope | National |
| Fields | name, surname, membership_number, province, primary wing, active_wings |
| Surfaces | API + PWA + mobile |

## API

`GET /api/v1/members`

- Middleware: `auth:sanctum` + ability `members:read`
- **Hard gate:** `MembershipStandingService::isFullMember` → else 403 `FULL_MEMBER_REQUIRED`
- Suspended never listed (standing ≠ member) and cannot call successfully
- Query: `q` (name/surname/membership_number LIKE), `province_id`, `wing` (active memberships), `page`
- Paginate 25
- Payload per row: `id`, `name`, `surname`, `membership_number`, `wing`, `active_wings`, `province: {id, name}` — **never** email, national_id, phone

## Permissions

- Add `members:read` to `config/permissions.php` → `member` role (and default only if appropriate — **not** student)
- Controllers still enforce full standing (provisional with `member` role after payment must not pass)

## Clients

- PWA: directory page + side menu (auth); show upgrade/locked message if not full member
- Mobile: same behaviour

## Out of scope

Opt-out, chat-from-directory, branch filter, public access, admin Members changes

## Tests

403 for applicant/provisional/suspended; 200 for full member; filters; assert JSON missing email/national_id
