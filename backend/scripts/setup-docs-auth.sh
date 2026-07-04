#!/usr/bin/env bash
# Enable HTTP basic auth for /docs/ (Scribe HTML, OpenAPI, Postman).
# Usage (repo root): bash backend/scripts/setup-docs-auth.sh <username>

set -euo pipefail

USERNAME="${1:-}"
if [[ -z "$USERNAME" ]]; then
  echo "Usage: bash backend/scripts/setup-docs-auth.sh <username>" >&2
  exit 1
fi

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
HTPASSWD="$ROOT/backend/storage/app/private/.htpasswd-docs"
NGINX_CONF="$ROOT/nginx.conf"

mkdir -p "$(dirname "$HTPASSWD")"

if command -v htpasswd >/dev/null 2>&1; then
  htpasswd -c "$HTPASSWD" "$USERNAME"
else
  echo "Install apache2-utils (htpasswd) and re-run." >&2
  exit 1
fi

if grep -q '# DOCS_AUTH_START' "$NGINX_CONF"; then
  awk '
    /# DOCS_AUTH_START/ { print; inblock=1; next }
    /# DOCS_AUTH_END/ {
      print "    location ^~ /docs/ {"
      print "        auth_basic \"ZANUPF API docs\";"
      print "        auth_basic_user_file /var/www/html/storage/app/private/.htpasswd-docs;"
      print "        try_files $uri $uri/ =404;"
      print "    }"
      print "    # DOCS_AUTH_END"
      inblock=0
      next
    }
    inblock { next }
    { print }
  ' "$NGINX_CONF" > "$NGINX_CONF.tmp" && mv "$NGINX_CONF.tmp" "$NGINX_CONF"
fi

echo ""
echo "Docs auth enabled for user: $USERNAME"
echo "  htpasswd: $HTPASSWD"
echo ""
echo "Reload nginx: docker compose exec nginx nginx -s reload"
