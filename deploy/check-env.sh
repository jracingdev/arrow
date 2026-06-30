#!/usr/bin/env bash
# Verifica .env dos painéis Laravel sem expor senhas.
# Uso: ./check-env.sh [WWW_ROOT]
set -euo pipefail

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

  local firebase_project
  firebase_project=$(grep '^FIREBASE_PROJECT_ID=' "$env_file" | cut -d= -f2- | tr -d '"')
  [[ -n "$firebase_project" ]] && echo "  [OK] FIREBASE_PROJECT_ID=$firebase_project" || echo "  [AVISO] FIREBASE_PROJECT_ID vazio"
  echo ""
}

echo "==> Verificação de .env em $WWW_ROOT"
echo ""

check_site "arrow_app_br" "https://arrow.app.br"
check_site "store_arrow_app_br" "https://store.arrow.app.br"
check_site "admin_arrow_app_br" "https://admin.arrow.app.br"

echo "Landing (lp_arrow_app_br): sem .env necessário."
