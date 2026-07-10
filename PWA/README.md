# ZANUPF Progressive Web App

Web port of the Expo mobile app (`../mobile/`). Same API, same dark Expo-style UI, phased feature parity.

## Quick start

```bash
cd PWA
npm install
npm run dev
```

Open **http://localhost:5173/app/** (Vite dev server proxies `/api` to `http://localhost:8081`).

## Production build

```bash
npm run build
```

Output: `../backend/public/app/` — served at **http://localhost:8081/app/** when Docker/nginx is running.

## Environment

Copy `.env.example` to `.env`:

```
VITE_API_BASE_URL=/api/v1
```

## Phase 1 (done)

- Splash (video + image from `src/assets/`)
- Auth: login, register, forgot password, guest browse
- Home overview + presidium + biography
- Constitutions: 3 documents, chapters, section reader (themes, bookmarks)
- Profile, static pages, about
- PWA manifest + service worker

## Phase 2 (done)

- **Academy**: courses, lessons, assessment flow, certificates status, payment receipt PDF
- **Library**: categories, documents
- **Party** + Party Organs + Leagues
- **Priority projects** with likes
- **Notifications**: in-app list + header bell badge (30s polling)

## Phase 3 (done)

- **Chat**: channels, threads, messages, Shona autocomplete, Reverb/Pusher live + polling fallback
- **Offline**: IndexedDB cache for parts, chapters, sections (stale-while-revalidate)
- **Reader**: section comments, copy, print, bookmarks, highlights (inline + list pages)
- **PWA polish**: install prompt, browser notifications when tab is in background
- **Assets**: copied from `mobile/assets/` into `src/assets/` and `public/`
- **Note**: Web Push (VAPID) supported when `WEBPUSH_*` keys are set on the backend; Profile enables subscription

## Polish (current)

- Shared auth layout (login, register, forgot password)
- Register terms/privacy/cookies links from app-config
- Route-based code splitting for academy, chat, and secondary pages
- Guest users: chat hidden on home grid and bottom tabs
- Section PDF export (print-to-PDF), Amendment 3 official PDF link
- Chapter read indicators, profile notification permission control

## Mobile reference

When implementing a feature, start from the equivalent file under `mobile/src/`:

| PWA | Mobile |
|-----|--------|
| `src/pages/LoginPage.jsx` | `src/screens/LoginScreen.js` |
| `src/pages/HomePage.jsx` | `src/screens/HomeScreen.js` |
| `src/pages/ConstitutionListPage.jsx` | `src/screens/ConstitutionListScreen.js` |
| `src/api/client.js` | `src/api/client.js` |

Assets live in `PWA/src/assets/` (copied from `mobile/assets/`). Splash video is `public/splash-video.mp4`.
