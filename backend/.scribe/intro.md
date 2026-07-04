# Introduction

JSON API for the ZANUPF mobile app and integrations (constitution reader, academy, dialogue, profile).

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8081</code>
</aside>

    All routes are prefixed with **`/api/v1`**. Example production base: `https://www.zanupf.org.zw/api/v1`.

    ### Authentication
    Most member features use **Laravel Sanctum** bearer tokens:

    1. `POST /api/v1/auth/login` or `POST /api/v1/auth/register`
    2. Send `Authorization: Bearer {access_token}` on subsequent requests
    3. Refresh via `POST /api/v1/auth/refresh` with the `refresh_token`

    Tokens are scoped by role (`profile:read`, `academy:write`, etc.). Public endpoints (constitution, library, party profile, health) do not require auth.

    ### Try It Out (browser tester)
    - **Login / Register** — use the pre-filled examples; register needs a **new** email each time.
    - **Refresh** — call Login first, copy `refresh_token` from the response, paste it into Refresh, then send.
    - **Protected routes** (Profile, Logout, Academy, …) — run **Login** first, click **Try it out** on the endpoint, then set the **Authorization** header to `Bearer {access_token}`. The same token is remembered across endpoints once entered.
    - **Forgot password** — use a seeded email such as `mobile.test@zanupf.org.zw`.

    ### Conventions
    | Topic | Convention |
    |-------|------------|
    | Content-Type | `application/json` for POST/PUT bodies |
    | Success | `{ "data": ... }` for resources |
    | Errors | `{ "message": "...", "errors": { ... } }` for validation |

    ### Regenerating docs
    `composer docs:api` or `bash scripts/generate-api-docs.sh` (Docker).

    <aside>Code examples appear in the dark panel to the right (or inline on mobile).</aside>

