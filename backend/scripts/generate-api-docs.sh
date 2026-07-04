#!/usr/bin/env bash
# Regenerate Scribe HTML, OpenAPI, and Postman collection.
# Usage (repo root): docker compose exec app bash scripts/generate-api-docs.sh
# Usage (backend):   bash scripts/generate-api-docs.sh
#
# Optional: set SCRIBE_AUTH_KEY in backend/.env to a valid bearer token so Scribe
# can call authenticated GET endpoints for live response samples.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

if [[ -f .env ]] && grep -q '^SCRIBE_AUTH_KEY=' .env; then
  export $(grep '^SCRIBE_AUTH_KEY=' .env | xargs)
  if [[ -n "${SCRIBE_AUTH_KEY:-}" ]]; then
    echo "Using SCRIBE_AUTH_KEY from .env for authenticated response samples."
  fi
fi

php artisan config:clear --ansi
php artisan scribe:generate --force --no-interaction

echo ""
echo "API docs generated:"
echo "  HTML:     ${APP_URL:-http://localhost:8081}/docs/index.html"
echo "  OpenAPI:  ${APP_URL:-http://localhost:8081}/docs/openapi.yaml"
echo "  Postman:  ${APP_URL:-http://localhost:8081}/docs/collection.json"
