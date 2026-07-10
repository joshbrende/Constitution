# Mobile app (Expo)

React Native client in `mobile/`. API contract: [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md).

> **Browser client:** The Progressive Web App in `PWA/` shares the same API. See [PWA.md](./PWA.md).

> **Note:** `mobile/` may be a git submodule. The full app tree lives in that repository; this doc describes the intended architecture and what is visible in the monorepo checkout.

---

## Stack

| Item | Value |
|------|-------|
| Framework | Expo / React Native |
| API base | `EXPO_PUBLIC_API_BASE_URL` (e.g. `http://192.168.x.x:8081/api/v1`) |
| Auth | Bearer token (Sanctum) via `mobile/src/api/client.js` |
| Health check | `mobile/src/api/healthApi.js` |

---

## Source layout (monorepo snapshot)

```
mobile/
├── src/
│   ├── api/           # HTTP client, health
│   ├── components/    # Shared UI (Skeleton, ErrorFallback, …)
│   ├── context/       # NetworkContext
│   ├── screens/       # MainNavigator + feature screens
│   └── utils/         # academyStatus, homeNavigation
├── .env.example
└── (app.json, package.json in full checkout)
```

### Screens (tracked in workspace)

| File | Purpose |
|------|---------|
| `SplashScreen.js` | Launch |
| `HomeScreen.js` | Home + banners |
| `MainNavigator.js` | Tab / stack navigation |
| `AcademyScreen.js` | Course list / enrol |
| `AcademyPortalScreen.js` | Applications, receipts, portal messages |
| `AssessmentResultScreen.js` | Post-exam result |
| `PaymentReceiptScreen.js` | Receipt view |
| `CertificatesScreen.js` | Certificate list (government workflow: status only) |

Additional screens referenced in [BACKEND-MOBILE-CONSISTENCY.md](./BACKEND-MOBILE-CONSISTENCY.md) (Login, Register, Constitution, Dialogue, Library, Profile) live in the full mobile repository.

---

## API dependencies (mobile)

| Feature | Endpoints |
|---------|-----------|
| Config | `GET /api/v1/app-config` |
| Auth | `POST /api/v1/auth/login`, `register`, `refresh`, `logout` |
| Home | `GET /api/v1/home-banners` |
| Constitution | `GET /api/v1/parts`, `chapters`, `sections/{id}`, search |
| Academy | `GET /api/v1/academy/courses`, enrol, assessments, attempts, summary |
| Applications | `GET /api/v1/academy/applications`, receipt PDF |
| Dialogue | `GET/POST /api/v1/dialogue/*` |
| Profile | `GET/PUT /api/v1/profile`, provinces |
| Static pages | `GET /api/v1/pages/{slug}` |

Full list: [generated/api-routes.json](./generated/api-routes.json).

---

## Offline / caching

Historical doc path: `mobile/docs/OFFLINE-MOBILE.md` (create in mobile repo if missing). Cross-stack notes: [BACKEND-MOBILE-CONSISTENCY.md §5](./BACKEND-MOBILE-CONSISTENCY.md).

---

## Rebuild note

The mobile client **cannot** be reconstructed from `docs/` alone. You need the `mobile/` git tree, `package.json`, Expo config, and asset bundle. Use this doc + API routes + consistency guide as specification when reimplementing.
