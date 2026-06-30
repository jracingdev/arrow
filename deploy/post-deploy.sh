#!/usr/bin/env bash
# Pós-deploy: composer, permissões e cache Laravel nos 3 painéis.
# Uso: ./post-deploy.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"

# aaPanel: CLI padrão costuma ser PHP 8.1; sites usam PHP 8.2
if [[ -z "${PHP_BIN:-}" ]] && [[ -x /www/server/php/82/bin/php ]]; then
  PHP_BIN="/www/server/php/82/bin/php"
else
  PHP_BIN="${PHP_BIN:-php}"
fi

COMPOSER_BIN="${COMPOSER_BIN:-$(command -v composer 2>/dev/null || echo /usr/bin/composer)}"
export COMPOSER_ALLOW_SUPERUSER=1

echo "==> Pós-deploy Laravel em $WWW_ROOT"
echo "    PHP: $($PHP_BIN -v | head -1)"
echo ""

if ! $PHP_BIN -m | grep -qi fileinfo; then
  echo "ERRO: ext-fileinfo ausente no PHP usado pelo deploy."
  echo "      Rode primeiro: sudo ./fix-php-aapanel.sh"
  exit 1
fi

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
      echo "    .env criado a partir de .env.example — CONFIGURE AS SENHAS!"
    else
      echo "    ERRO: .env ausente — configure manualmente."
      continue
    fi
  fi

  if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    $PHP_BIN artisan key:generate --force
    echo "    APP_KEY gerada."
  fi

  if [[ -f "$COMPOSER_BIN" ]] || command -v composer &>/dev/null; then
    $PHP_BIN "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction
    echo "    composer install OK."
  else
    echo "    AVISO: composer não encontrado."
  fi

  mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

  chown -R www:www "$SITE_PATH/storage" "$SITE_PATH/bootstrap/cache" 2>/dev/null || true
  chmod -R 775 storage bootstrap/cache 2>/dev/null || true

  $PHP_BIN artisan storage:link 2>/dev/null || true
  $PHP_BIN artisan config:clear
  $PHP_BIN artisan config:cache
  $PHP_BIN artisan route:cache
  $PHP_BIN artisan view:cache

  echo "    Cache Laravel OK."
  echo ""
done

echo "==> Pós-deploy concluído."
echo "    Próximo: sudo ./set-production.sh"
