#!/usr/bin/env bash
# Ajusta .env para produção nos 3 painéis Laravel.
# Uso: ./set-production.sh [WWW_ROOT]
#
# Define: APP_ENV=production, APP_DEBUG=false,
#         APP_TIMEZONE=America/Sao_Paulo, LOCALE=pt_br
# Depois: php artisan config:cache em cada site.
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

upsert_env() {
  local file="$1"
  local key="$2"
  local value="$3"
  if grep -q "^${key}=" "$file" 2>/dev/null; then
    sed -i "s|^${key}=.*|${key}=${value}|" "$file"
  else
    printf '\n%s=%s\n' "$key" "$value" >> "$file"
  fi
}

echo "==> Ajustando .env para produção"
echo ""

for site in "${LARAVEL_SITES[@]}"; do
  ENV_FILE="$WWW_ROOT/$site/.env"
  if [[ ! -f "$ENV_FILE" ]]; then
    echo "AVISO: $ENV_FILE não encontrado — pulando."
    continue
  fi

  upsert_env "$ENV_FILE" APP_ENV production
  upsert_env "$ENV_FILE" APP_DEBUG false
  upsert_env "$ENV_FILE" APP_TIMEZONE America/Sao_Paulo
  upsert_env "$ENV_FILE" LOCALE pt_br

  echo "---- $site ----"
  echo "    APP_ENV=production APP_DEBUG=false"
  echo "    APP_TIMEZONE=America/Sao_Paulo LOCALE=pt_br"

  if [[ -f "$WWW_ROOT/$site/artisan" ]]; then
    (cd "$WWW_ROOT/$site" && $PHP_BIN artisan config:cache)
    echo "    config:cache OK"
  fi
  echo ""
done

echo "==> Produção configurada."
echo "    Valide com: ./check-env.sh"
