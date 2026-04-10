# 9. Digital Library (admin)

## 9.1 Purpose

Manage **categories** and **documents** for the party digital library (policy papers, historical material, etc.).

## 9.2 Admin routes

| Feature | Route pattern |
|---------|----------------|
| Hub | `admin.library.index` |
| Categories | `admin.library.categories.index`, `.create`, `.store`, `.edit`, `.update`, `.destroy` |
| Documents | `admin.library.documents.index`, `.create`, `.store`, `.edit`, `.update`, `.destroy` |

**Controller:** `App\Http\Controllers\Admin\LibraryController`

## 9.3 Web reader

- `library.home`, `library.document` — authenticated web views (`WebLibraryController`).

## 9.4 API

- `GET /api/v1/library/categories` — public  
- `GET /api/v1/library/documents` — filtered by access rules  
- `GET /api/v1/library/documents/{document}` — document detail  

See [26-api-public-content.md](./26-api-public-content.md).

---

*Last reviewed: documentation generation pass.*
