#!/usr/bin/env bash
# Ajusta .env para produção nos 3 painéis Laravel.
# Uso: ./set-production.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"

if [[ -z "${PHP_BIN:-}" ]] && [[ -x /www/server/php/82/bin/php ]]; then
  PHP_BIN="/www/server/php/82/bin/php"
else
  PHP_BIN="${PHP_BIN:-php}"
fi

echo "==> Ajustando .env para produção"
echo ""

for site in "${LARAVEL_SITES[@]}"; do
  ENV_FILE="$WWW_ROOT/$site/.env"
  if [[ ! -f "$ENV_FILE" ]]; then
    echo "AVISO: $ENV_FILE não encontrado — pulando."
    continue
  fi

  sed -i 's/^APP_ENV=.*/APP_ENV=production/' "$ENV_FILE"
  sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' "$ENV_FILE"

  echo "---- $site ----"
  echo "    APP_ENV=production, APP_DEBUG=false"

  if [[ -f "$WWW_ROOT/$site/artisan" ]]; then
    (cd "$WWW_ROOT/$site" && $PHP_BIN artisan config:cache)
    echo "    config:cache OK"
  fi
  echo ""
done

echo "==> Produção configurada."
