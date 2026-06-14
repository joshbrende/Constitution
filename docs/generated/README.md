# Generated documentation exports

Machine-readable snapshots checked into git for disaster recovery and diff review.

---

## Files

| File | Contents | Regenerate |
|------|----------|------------|
| [api-routes.json](./api-routes.json) | Full Laravel route list (method, URI, name, action, middleware) | See below |

---

## Regenerate `api-routes.json`

From repository root with Docker running:

```bash
docker compose exec app php artisan route:list --json > docs/generated/api-routes.json
```

Or locally (PHP 8.2+):

```bash
cd backend
php artisan route:list --json > ../docs/generated/api-routes.json
```

Commit after adding or renaming routes in `routes/web.php` or `routes/api.php`.

---

## Future exports (recommended)

| Export | Command / source |
|--------|------------------|
| Schema dump | `php artisan schema:dump` → `database/schema/mysql-schema.sql` (optional in git) |
| OpenAPI | Not yet generated — would require dedicated tooling |

See [RECONSTRUCTION.md](../RECONSTRUCTION.md) for why these matter.
