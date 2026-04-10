# 26. API — Public & mixed content

Routes in `routes/api.php` **without** requiring Sanctum unless noted.

## Digital library

**Controller:** `Api\LibraryController`

| Method | Path |
|--------|------|
| GET | `/api/v1/library/categories` |
| GET | `/api/v1/library/documents` |
| GET | `/api/v1/library/documents/{document}` |

## Party & organs & Presidium

| Method | Path | Controller |
|--------|------|--------------|
| GET | `/api/v1/party-organs` | `PartyOrgansController@index` |
| GET | `/api/v1/party-organs/{party_organ}` | `show` |
| GET | `/api/v1/presidium` | `PresidiumController@index` |
| GET | `/api/v1/party/profile` | `PartyController@profile` |

## Home banners

| GET | `/api/v1/home-banners` |

Mobile clients should treat **failure** (network, 5xx) differently from an **empty** `data` array: the Overview screen surfaces a notice when the banners request fails.

## Official constitution documents

| GET | `/api/v1/constitution/official/amendment3` | `Api\ConstitutionOfficialController@amendment3` |

Returns JSON: `available` (bool), `title` (string), and when `available` is true, `url` (absolute URL built from the **request host** so LAN/mobile devices open the correct origin). The file is stored at `storage/app/public/` path configured in `config/constitution.php` (default `constitution-official/amendment3.pdf`). Administrators replace it via **Admin → Constitution management → Official Amendment Bill PDF**.

## Static pages

| GET | `/api/v1/pages/{slug}` | `StaticPagesController@show` |

## Health

| GET | `/api/v1/health` | Uptime monitoring |

## Constitution content (read)

**Controllers:** `PartController`, `ChapterController`, `SectionController`, `CommentController`

| Method | Path | Auth |
|--------|------|------|
| GET | `/api/v1/parts` | No |
| GET | `/api/v1/chapters` | No |
| GET | `/api/v1/chapters/{chapter}` | No |
| GET | `/api/v1/sections/search` | No |
| GET | `/api/v1/sections/{section}` | No |
| GET | `/api/v1/sections/{section}/comments` | No |
| POST | `/api/v1/sections/{section}/comments` | **Sanctum** |

## Priority projects

| GET | `/api/v1/priority-projects` | No |
| POST | `/api/v1/priority-projects/{priority_project}/like` | Sanctum |

---

*Last reviewed: documentation generation pass.*
