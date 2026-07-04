# Introduction

JSON API for the ZANUPF mobile app and integrations (constitution reader, academy, dialogue, profile).

<aside>
    <strong>Base URL</strong>: <code>http://localhost/api/v1</code>
</aside>

    All routes are prefixed with **`/api/v1`**. Example production base: `https://www.zanupf.org.zw/api/v1`.

    ### Authentication
    Most member features use **Laravel Sanctum** bearer tokens:

    1. `POST /api/v1/auth/login` or `POST /api/v1/auth/register`
    2. Send `Authorization: Bearer {access_token}` on subsequent requests
    3. Refresh via `POST /api/v1/auth/refresh` with the `refresh_token`

    Tokens are scoped by role (`profile:read`, `academy:write`, etc.). Public endpoints (constitution, library, party profile, health) do not require auth.

    ### Conventions
    | Topic | Convention |
    |-------|------------|
    | Content-Type | `application/json` for POST/PUT bodies |
    | Success | `{ "data": ... }` for resources |
    | Errors | `{ "message": "...", "errors": { ... } }` for validation |

    ### Regenerating docs
    `composer docs:api` or `bash scripts/generate-api-docs.sh` (Docker).

    <aside>Code examples appear in the dark panel to the right (or inline on mobile).</aside>

