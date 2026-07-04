# Seed data inventory

What `php artisan db:seed` and the **setup wizard** (step 5) load into the database. **Content bodies are in PHP seeders and data files in git** — not duplicated in this doc.

---

## Entry points

| Command / flow | Seeder entry |
|----------------|--------------|
| Full dev seed | `DatabaseSeeder` → 18 seeder classes + optional `MobileTestUserSeeder` |
| Setup wizard step 5 | `SetupInstallService::seedPlatformContent()` — same core list, optional mobile test user checkbox |
| Wizard admin step | `RoleSeeder` + `PermissionSeeder` only (before first admin user) |

---

## Seeder catalog

| Seeder | Produces |
|--------|----------|
| `RoleSeeder` | Roles (`system_admin`, `student`, `member`, `content_editor`, …) |
| `PermissionSeeder` | Permissions + role attachments |
| `AdminUserSeeder` | Optional `admin@zanupf.org.zw` if `ADMIN_SEED_PASSWORD` set |
| `ConstitutionSeeder` | ZANU PF constitution structure + section text |
| `ZimbabweConstitutionSeeder` | Zimbabwe 2013 constitution structure |
| `AmendmentBill2026Seeder` | Amendment Bill 2026 content |
| `AmendmentBill2026MetaSyncSeeder` | Amendment metadata sync |
| `MembershipCourseSeeder` | Membership course, modules, lessons, assessment questions |
| `DialogueSeeder` | Default dialogue channels |
| `PartyProfileSeeder` | Party profile copy |
| `PartyOrgansSeeder` | Party organs |
| `PartyLeaguesSeeder` | Leagues |
| `PresidiumSeeder` | Presidium members |
| `PriorityProjectsSeeder` | Vision 2030 / priority projects |
| `AcademyBadgesSeeder` | Course badge criteria |
| `HomeBannersSeeder` | Mobile home carousel (`cab3.jpg`, `vision-2030.jpg`, … in `public/`) |
| `LibrarySeeder` | Library categories and documents |
| `StaticPagesSeeder` | Help, terms, privacy, cookies (`static_pages`) |
| `MobileTestUserSeeder` | `mobile.test@zanupf.org.zw` (wizard optional; skip production) |

---

## External / optional content

| Source | Trigger | Notes |
|--------|---------|-------|
| `storage/app/zimbabwe-constitution-source.txt` | `constitution:import-zimbabwe` after seed | Large text import |
| `storage/app/public/constitution-official/amendment3.pdf` | Manual upload (admin or filesystem) | Official PDF for API |
| `membership-course-plan.md` | Product spec only | Sample questions; full bank is in `MembershipCourseSeeder` |

---

## What cannot be recreated from docs alone

- Full constitution article text (thousands of lines in seeders / import files)
- Complete assessment question bank
- Library document PDF binaries
- Banner image binaries (`backend/public/*.jpg`)

**Backup strategy:** git repo + database dump + `storage/app/public` archive.

---

## Product planning reference

Partial membership course design (modules 1–9 samples): [membership-course-plan.md](./membership-course-plan.md).
