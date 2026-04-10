# Integrations (government ICT / internal network)

This deployment is intended for **internal government servers** (controlled network). To comply with internal ICT policies, external integrations must be explicit, allowlisted, and auditable.

## Principles
- **Deny-by-default outbound**: the backend must only call approved internal services.
- **Single integration entrypoint**: do not call other services directly from controllers.
- **Traceability**: include `X-Request-Id` on outbound calls and log it.
- **Least privilege**: use service identities/credentials with minimal scope.

## Approved patterns (pick per service)
- **API gateway** (preferred where available): gateway terminates auth, enforces policy.
- **Direct internal service calls**: private network + allowlisted hosts; ideally **mTLS**.
- **Message bus**: async integration where appropriate (audit-heavy workflows).

## How to add a new internal service
1. Add the service base URL to env/config (never hardcode).
2. Add its host to the outbound allowlist.
3. Implement calls through the integration wrapper:
   - timeouts
   - retries (if safe)
   - consistent headers (`X-Request-Id`)
4. Add audit logging if the call is privileged or affects records.

## Configuration (backend)
- `INTEGRATION_ALLOWLIST_HOSTS`
  - Comma-separated hosts allowed for outbound calls.
  - Example: `idm.gov.local,gateway.gov.local,hr.gov.local`

## Implementation in this repo
- Backend integration wrapper:
  - `backend/app/Support/GovIntegrationClient.php`
- Config:
  - `backend/config/integrations.php`

