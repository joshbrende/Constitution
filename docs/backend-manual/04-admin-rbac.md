# 4. Admin entry & RBAC

## 4.1 Entry points

| URL | Name | Middleware |
|-----|------|------------|
| `/admin` | `admin.home` | `auth`, `admin.content`, `admin.section` |

All routes under `admin.*` share the same middleware stack (see `routes/web.php`).

## 4.2 Middleware

| Alias | Class | Behaviour |
|-------|--------|-----------|
| `admin.content` | `EnsureAdminOrContentEditor` | User must have **any** role listed across `config/admin.php` sections (via `AdminAccessService::hasAnyAdminAccess()`). |
| `admin.section` | `EnsureAdminSection` | For each request, infers **section** from route name (`admin.constitution.*` → `constitution`, etc.) and checks `AdminAccessService::canAccessSection()`. `admin.home` has no section gate — any user passing `admin.content` may open the hub. |
| `presidium` | `EnsurePresidiumAccess` | Only `presidium` or `system_admin` — used on constitution amendment **approve/reject** routes. |

## 4.3 Configuration

**File:** `backend/config/admin.php`

Key `sections` maps **section slug** → **array of role slugs** that may access that admin area.

Examples:

- `constitution`: `system_admin`, `content_editor`, `approver`, `presidium`
- `academy`: above + `academy_manager`
- `dialogue`: above + `dialogue_moderator`, `moderator`
- `users` / `members`: + `user_manager`, `provincial_admin`
- `analytics`: + `analytics_viewer`
- `audit_logs`: `system_admin`, `presidium`, `audit_viewer`
- `roles`: **`system_admin` only**

## 4.4 Services

- **`App\Services\AdminAccessService`** — `canAccessSection($user, $section)`, `hasAnyAdminAccess($user)`, `getAccessibleSections($user)`.

## 4.5 Blade

- **`@canAccessSection('section')`** — registered in `AppServiceProvider` for conditional nav/tiles.

## 4.6 Related

- [05-roles-users.md](./05-roles-users.md) — assigning roles  
- [`../../backend/docs/role-workflows.md`](../../backend/docs/role-workflows.md) — amendment workflow copy  

---

*Last reviewed: documentation generation pass.*
