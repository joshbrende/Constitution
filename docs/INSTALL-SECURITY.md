# Installation security

## Setup wizard access

The installation wizard (`/setup/*`) is **public only until** `site_settings.installed_at` is set. After completion, setup routes return 404.

### Production requirement

1. Generate a long random token:
   ```bash
   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
   ```
2. Set in `backend/.env`:
   ```env
   SETUP_ACCESS_TOKEN=your-generated-token
   ```
3. Open the wizard only with the token:
   - URL: `https://your-domain/setup?setup_token=your-generated-token`
   - Or header: `X-Setup-Token: your-generated-token`

If `APP_ENV=production` and `SETUP_ACCESS_TOKEN` is empty, setup returns **503** (installation locked).

### Network controls

During install, additionally:

- Restrict `/setup` to installer IP via firewall or VPN.
- Complete the wizard in one session.
- Change the system administrator password immediately after install.
- Remove or rotate `SETUP_ACCESS_TOKEN` after install (optional; routes are disabled once `installed_at` is set).

## Post-install checklist

See [PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md) and the wizard finish-step production checklist.

## Compromise response

If unauthorized access to `/setup` is suspected before completion:

1. Block network access to the host.
2. Do not complete install until reviewed.
3. Rotate database credentials and `APP_KEY` if install was partially completed.
4. See [INCIDENT-RESPONSE.md](./INCIDENT-RESPONSE.md).
