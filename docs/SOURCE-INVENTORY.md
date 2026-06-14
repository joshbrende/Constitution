# Source inventory

Snapshot of the **Constitution Platform** repository structure. Counts are approximate; verify with git when preparing releases.

---

## Repository layout

```
constitution/
├── backend/          # Laravel 12 API + Blade admin + web reader
├── mobile/           # Expo React Native app (submodule / separate tree)
├── docs/             # This documentation set
├── docker-compose.yml
├── nginx.conf
├── DOCKER.md
└── compose.env.example
```

---

## Backend (`backend/`)

| Area | Path | Count (approx.) |
|------|------|-----------------|
| Migrations | `database/migrations/` | 67 |
| Eloquent models | `app/Models/` | 49 |
| HTTP controllers | `app/Http/Controllers/` | 63 |
| Domain services | `app/Services/` | 27 (incl. 5 Setup/*) |
| Policies | `app/Policies/` | 12 |
| Blade views | `resources/views/` | ~110 |
| Feature tests | `tests/Feature/` | ~30 |
| Seeders | `database/seeders/` | 24 |
| Route files | `routes/web.php`, `routes/api.php` | ~168 route entries |

### Critical paths

| Path | Role |
|------|------|
| `routes/api.php` | Mobile + public JSON API (`/api/v1/*`) |
| `routes/web.php` | Auth, dashboard, admin CRUD, setup wizard |
| `config/admin.php` | Admin section → role slugs |
| `config/permissions.php` | Permission definitions + Sanctum abilities |
| `config/academy.php` | Certificate fee, payment offices, role grant timing |
| `app/Services/CertificateApplicationService.php` | Government certificate workflow |
| `app/Services/CertificatePdfService.php` | TCPDF certificate generation |
| `app/Services/ReceiptPdfService.php` | Payment receipt PDF |
| `app/Http/Controllers/SetupWizardController.php` | First-run install wizard |
| `app/Services/Setup/*` | System checks, DB provision, seed orchestration |

### Setup wizard (first install)

| Step | Route name | Controller |
|------|------------|------------|
| Welcome | `setup.index` | `welcome` |
| Checks | `setup.checks` | `checks` |
| Continue | `setup.continue` | `continueFromChecks` |
| Admin | `setup.admin` / `setup.admin.store` | `showAdmin` / `storeAdmin` |
| Platform | `setup.platform` / `setup.platform.store` | `showPlatform` / `storePlatform` |
| Seed | `setup.seed` / `setup.seed.run` | `showSeed` / `runSeed` |
| Finish | `setup.finish` / `setup.complete` | `finish` / `complete` |

Middleware: `setup.pending`, `setup.sync`, `install.complete`.

Views: `resources/views/setup/` (layout, welcome, checks, admin, platform, seed, finish, partials).

---

## Mobile (`mobile/`)

Expo React Native client. See [MOBILE-APP.md](./MOBILE-APP.md).

Env: `EXPO_PUBLIC_API_BASE_URL` (must include `/api/v1`).

---

## Generated exports (`docs/generated/`)

| File | Regenerate |
|------|------------|
| `api-routes.json` | `php artisan route:list --json` |

See [generated/README.md](./generated/README.md).

---

## External assets not in git (typical)

| Asset | Location |
|-------|----------|
| Production `.env` | Server / hosting panel |
| Official amendment PDF | `storage/app/public/constitution-official/amendment3.pdf` |
| Zimbabwe constitution import source | `storage/app/zimbabwe-constitution-source.txt` (optional) |
| User uploads | `storage/app/public/` |
| Generated certificate PDFs | `storage/app/` (paths on `certificates` table) |
