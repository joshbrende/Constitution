# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_ACCESS_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Obtain a token with `POST /api/v1/auth/login` (email + password). Example:

```bash
curl -X POST "{APP_URL}/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"member@example.org.zw","password":"your-password"}'
```

Use the returned `access_token` as `Authorization: Bearer {token}`. Refresh via `POST /api/v1/auth/refresh`.
