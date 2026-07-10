# 12. Priority projects & home banners

## 12.1 Priority projects

- **Admin:** `admin.priority-projects.*` — CRUD for Vision 2030–aligned projects
- **Controller:** `Admin\PriorityProjectsController`
- **API:** `GET /api/v1/priority-projects`, `GET …/{id}`, `POST …/{id}/like` (all Sanctum; see [26-api-public-content.md](./26-api-public-content.md))

## 12.2 Home banners (carousel)

- **Admin:** `admin.home-banners.*`
- **Controller:** `Admin\HomeBannersController`
- **API:** `GET /api/v1/home-banners` — public, for mobile Overview carousel

---

*Last reviewed: documentation generation pass.*
