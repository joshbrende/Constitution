# Generated documentation exports

Machine-readable snapshots and generated API reference assets.

---

## Files

| File / folder | Contents | Regenerate |
|---------------|----------|------------|
| [api-routes.json](./api-routes.json) | Full Laravel route list (method, URI, name, action, middleware) | `php artisan route:list --json` |
| `backend/public/docs/` | Scribe HTML + OpenAPI + Postman (gitignored) | `composer docs:api` |

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

## Regenerate Scribe API docs

From repository root:

```bash
docker compose exec app bash scripts/generate-api-docs.sh
```

Or locally:

```bash
cd backend
composer docs:api
```

### Deploy checklist

1. Deploy application code (including any `@group` / Scribe annotation changes).
2. Run migrations if needed.
3. Run `composer docs:api` on the server or in CI before packaging the release.
4. Ensure `public/docs/` is included in the deployed artifact (or generated on the server post-deploy).
5. In production, protect `/docs/` (nginx `auth_basic`, VPN, or staging-only hosting).

### Environment variables

| Variable | Purpose |
|----------|---------|
| `SCRIBE_AUTH_KEY` | Optional bearer token for live authenticated response samples during generation |
| `SCRIBE_TRY_IT_OUT` | Set `false` in production if docs are hosted elsewhere |

Intro and auth copy are maintained in `backend/config/scribe.php` (`intro_text`, `auth.extra_info`). Regeneration uses `--force`, which refreshes `.scribe/intro.md` from config.

---

## Future exports (recommended)

| Export | Command / source |
|--------|------------------|
| Schema dump | `php artisan schema:dump` → `database/schema/mysql-schema.sql` (optional in git) |

See [RECONSTRUCTION.md](../RECONSTRUCTION.md) for why these matter.
