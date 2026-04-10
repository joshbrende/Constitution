# 22. API — Profile & provinces

## Profile

**Controller:** `App\Http\Controllers\Api\ProfileController`

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/v1/profile` | Returns `{ "data": user }` with roles and province |
| PUT | `/api/v1/profile` | Update name, surname, national_id, province, etc. |

## Provinces

**Controller:** `App\Http\Controllers\Api\ProvinceController`

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/v1/provinces` | List for profile picker |

---

*Last reviewed: documentation generation pass.*
