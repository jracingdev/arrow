#!/usr/bin/env bash
# Pós-deploy: composer, permissões e cache Laravel nos 3 painéis.
# Uso: ./post-deploy.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"
PHP_BIN="${PHP_BIN:-php}"

echo "==> Pós-deploy Laravel em $WWW_ROOT"
echo ""

for site in "${LARAVEL_SITES[@]}"; do
  SITE_PATH="$WWW_ROOT/$site"

  if [[ ! -f "$SITE_PATH/artisan" ]]; then
    echo "AVISO: $SITE_PATH não é Laravel — pulando."
    continue
  fi

  echo "---- $site ----"
  cd "$SITE_PATH"

  if [[ ! -f .env ]]; then
    if [[ -f .env.example ]]; then
      cp .env.example .env
      echo "    .env criado a partir de .env.example — CONFIGURE AS SENHAS antes de usar em produção!"
    else
      echo "    ERRO: .env ausente e sem .env.example — configure manualmente."
      continue
    fi
  fi

  if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    $PHP_BIN artisan key:generate --force
    echo "    APP_KEY gerada."
  fi

  if command -v composer &>/dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
    echo "    composer install OK."
  else
    echo "    AVISO: composer não encontrado no PATH."
  fi

  mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

  if id www &>/dev/null; then
    chown -R www:www storage bootstrap/cache 2>/dev/null || true
  fi
  chmod -R 775 storage bootstrap/cache 2>/dev/null || true

  $PHP_BIN artisan storage:link 2>/dev/null || true
  $PHP_BIN artisan config:cache
  $PHP_BIN artisan route:cache
  $PHP_BIN artisan view:cache

  echo "    Cache Laravel OK."
  echo ""
done

echo "==> Pós-deploy concluído."
echo "    Verifique: APP_DEBUG=false e DB_PASSWORD em cada .env"
