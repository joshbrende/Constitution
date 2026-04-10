# 13. Static pages (admin)

## 13.1 Purpose

Edit **Help**, **Terms**, **Privacy**, and other fixed content pages consumed by the app.

## 13.2 Routes

- `admin.static-pages.index` — list
- `admin.static-pages.edit`, `admin.static-pages.update` — per `{page}` key

**Controller:** `App\Http\Controllers\Admin\StaticPagesController`

## 13.3 API

- `GET /api/v1/pages/{slug}` — `ApiStaticPagesController@show`

---

*Last reviewed: documentation generation pass.*
