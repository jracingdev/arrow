#!/usr/bin/env bash
# Verifica .env dos painéis Laravel sem expor senhas.
# Uso: ./check-env.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"

check_site() {
  local site="$1"
  local expected_url="$2"
  local env_file="$WWW_ROOT/$site/.env"

  echo "---- $site ----"

  if [[ ! -f "$env_file" ]]; then
    echo "  [FALTA] .env não existe"
    return
  fi

  local app_url app_env app_debug app_key db_name db_user db_pass

  app_url=$(grep '^APP_URL=' "$env_file" | cut -d= -f2- | tr -d '"')
  app_env=$(grep '^APP_ENV=' "$env_file" | cut -d= -f2- | tr -d '"')
  app_debug=$(grep '^APP_DEBUG=' "$env_file" | cut -d= -f2- | tr -d '"')
  app_key=$(grep '^APP_KEY=' "$env_file" | cut -d= -f2-)
  db_name=$(grep '^DB_DATABASE=' "$env_file" | cut -d= -f2- | tr -d '"')
  db_user=$(grep '^DB_USERNAME=' "$env_file" | cut -d= -f2- | tr -d '"')
  db_pass=$(grep '^DB_PASSWORD=' "$env_file" | cut -d= -f2- | tr -d '"')

  [[ "$app_url" == "$expected_url" ]] && echo "  [OK] APP_URL=$app_url" || echo "  [AJUSTAR] APP_URL=$app_url (esperado: $expected_url)"
  [[ "$app_env" == "production" ]] && echo "  [OK] APP_ENV=production" || echo "  [AJUSTAR] APP_ENV=$app_env"
  [[ "$app_debug" == "false" ]] && echo "  [OK] APP_DEBUG=false" || echo "  [AJUSTAR] APP_DEBUG=$app_debug"
  [[ "$app_key" == base64:* ]] && echo "  [OK] APP_KEY definida" || echo "  [FALTA] APP_KEY vazia — rode: php artisan key:generate"
  [[ -n "$db_name" ]] && echo "  [OK] DB_DATABASE=$db_name" || echo "  [FALTA] DB_DATABASE"
  [[ -n "$db_user" ]] && echo "  [OK] DB_USERNAME=$db_user" || echo "  [FALTA] DB_USERNAME"
  [[ -n "$db_pass" ]] && echo "  [OK] DB_PASSWORD definida" || echo "  [FALTA] DB_PASSWORD — copie do aaPanel"

  local firebase_vars=(
    FIREBASE_APIKEY
    FIREBASE_AUTH_DOMAIN
    FIREBASE_DATABASE_URL
    FIREBASE_PROJECT_ID
    FIREBASE_STORAGE_BUCKET
    FIREBASE_MESSAAGING_SENDER_ID
    FIREBASE_APP_ID
    FIREBASE_MEASUREMENT_ID
  )
  local var val
  for var in "${firebase_vars[@]}"; do
    val=$(grep "^${var}=" "$env_file" | cut -d= -f2- | tr -d '"' | tr -d "'")
    if [[ -n "$val" ]]; then
      if [[ "$var" == "FIREBASE_PROJECT_ID" ]]; then
        echo "  [OK] $var=$val"
      else
        echo "  [OK] $var definida"
      fi
    else
      echo "  [FALTA] $var vazia — Firebase no browser não inicializa"
    fi
  done

  local sw_file="$WWW_ROOT/$site/public/firebase-messaging-sw.js"
  if [[ "$site" == "$WWW_WEBSITE" ]]; then
    [[ -f "$sw_file" ]] && echo "  [OK] firebase-messaging-sw.js existe" || echo "  [FALTA] firebase-messaging-sw.js — rode: ./fix-firebase-config.sh"
  fi
  echo ""
}

echo "==> Verificação de .env em $WWW_ROOT"
echo ""

check_site "$WWW_WEBSITE" "https://arrow.app.br"
check_site "$WWW_STORE" "https://store.arrow.app.br"
check_site "$WWW_ADMIN" "https://admin.arrow.app.br"

echo "Landing ($WWW_LANDING): sem .env necessário."
