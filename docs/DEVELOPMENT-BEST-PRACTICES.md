# Development best practices (this repo)

This repo is **Laravel 12** (`backend/`) + an **Expo React Native** app (`mobile/`). Some “best practices” lists (e.g. Convex) are framework-specific; this document captures the *equivalent guidance for our actual stack*.

## Backend (`backend/`) — Laravel / MySQL / Sanctum

### Validation (request boundary)
- Prefer **Form Requests** for any non-trivial endpoint.
- Validate **types + ranges + existence** (e.g. `exists:...`) and keep validation rules close to the controller boundary.
- Normalize booleans and optional fields explicitly (avoid “truthy string” surprises).

### AuthN/AuthZ
- Put **authentication** behind middleware (`auth:sanctum`) for protected routes.
- Put **authorization** in **Policies/Gates** (not scattered `if (...) abort(403)` checks).
- Scope admin actions to a single “root” authorization decision when possible (e.g. course-scoped LMS edits).

### Query performance
- Avoid N+1: use `->with(...)`, `->withCount(...)`.
- Use pagination for list endpoints (`paginate()` / `simplePaginate()`).
- Add DB indexes for frequently searched/sorted columns and foreign keys.

### Error handling (API)
- Use consistent JSON error shapes and status codes (401/403/404/422/429/5xx).
- Do not leak raw exception messages in production responses.

### Background work & scheduling
- Use queues for slow work (PDF generation, emails, heavy exports).
- Use Laravel Scheduler for *internal jobs*; don’t expose scheduler entrypoints as public routes.

## Mobile (`mobile/`) — Expo / React Native (JavaScript)

### Async handling + UX
- Always `await` async calls and handle failures in one place.
- Prefer a consistent “API error → UI” mapping (friendly messages, stable error kinds).

### Networking best practices (Axios)
- Centralize token attach/refresh/retry logic in the API client layer.
- Treat 401/403/429/503 as normal states (session expired, forbidden, rate limited, busy).
- Use pagination or incremental loading for large datasets.

### State / component organization
- Keep screens thin: route params + UI orchestration.
- Keep domain logic in `src/api/*`, `src/offline/*`, and reusable components/hooks.

### Linting & formatting (non-blocking)
- `npm run lint` reports issues but **won’t fail builds** (`--max-warnings=999999`).
- `npm run format` standardizes formatting (Prettier).

