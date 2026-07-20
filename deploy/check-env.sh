#!/usr/bin/env bash
# Verifica .env dos painéis Laravel sem expor senhas.
# Uso: ./check-env.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"
EXPECTED_TZ="America/Sao_Paulo"
EXPECTED_LOCALE="pt_br"
FAILS=0

env_val() {
  local file="$1"
  local key="$2"
  grep "^${key}=" "$file" 2>/dev/null | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r'
}

check_eq() {
  local label="$1"
  local actual="$2"
  local expected="$3"
  if [[ "$actual" == "$expected" ]]; then
    echo "  [OK] $label=$actual"
  else
    echo "  [AJUSTAR] $label=${actual:-<vazio>} (esperado: $expected)"
    FAILS=$((FAILS + 1))
  fi
}

check_nonempty() {
  local label="$1"
  local actual="$2"
  local hint="${3:-}"
  if [[ -n "$actual" ]]; then
    echo "  [OK] $label definida"
  else
    echo "  [FALTA] $label vazia${hint:+ — $hint}"
    FAILS=$((FAILS + 1))
  fi
}

check_site() {
  local site="$1"
  local expected_url="$2"
  local env_file="$WWW_ROOT/$site/.env"

  echo "---- $site ----"

  if [[ ! -f "$env_file" ]]; then
    echo "  [FALTA] .env não existe"
    FAILS=$((FAILS + 1))
    echo ""
    return
  fi

  local app_url app_env app_debug app_key app_tz locale db_name db_user db_pass
  local cred_file

  app_url=$(env_val "$env_file" APP_URL)
  app_env=$(env_val "$env_file" APP_ENV)
  app_debug=$(env_val "$env_file" APP_DEBUG)
  app_key=$(env_val "$env_file" APP_KEY)
  app_tz=$(env_val "$env_file" APP_TIMEZONE)
  locale=$(env_val "$env_file" LOCALE)
  db_name=$(env_val "$env_file" DB_DATABASE)
  db_user=$(env_val "$env_file" DB_USERNAME)
  db_pass=$(env_val "$env_file" DB_PASSWORD)

  check_eq "APP_URL" "$app_url" "$expected_url"
  check_eq "APP_ENV" "$app_env" "production"
  check_eq "APP_DEBUG" "$app_debug" "false"
  check_eq "APP_TIMEZONE" "$app_tz" "$EXPECTED_TZ"
  check_eq "LOCALE" "$locale" "$EXPECTED_LOCALE"

  if [[ "$app_key" == base64:* ]]; then
    echo "  [OK] APP_KEY definida"
  else
    echo "  [FALTA] APP_KEY vazia — rode: php artisan key:generate"
    FAILS=$((FAILS + 1))
  fi

  [[ -n "$db_name" ]] && echo "  [OK] DB_DATABASE=$db_name" || { echo "  [FALTA] DB_DATABASE"; FAILS=$((FAILS + 1)); }
  [[ -n "$db_user" ]] && echo "  [OK] DB_USERNAME=$db_user" || { echo "  [FALTA] DB_USERNAME"; FAILS=$((FAILS + 1)); }
  check_nonempty "DB_PASSWORD" "$db_pass" "copie do aaPanel"

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
    val=$(env_val "$env_file" "$var")
    if [[ -n "$val" ]]; then
      if [[ "$var" == "FIREBASE_PROJECT_ID" ]]; then
        echo "  [OK] $var=$val"
      else
        echo "  [OK] $var definida"
      fi
    else
      echo "  [FALTA] $var vazia — Firebase no browser não inicializa"
      FAILS=$((FAILS + 1))
    fi
  done

  cred_file="$WWW_ROOT/$site/storage/app/firebase/credentials.json"
  if [[ -f "$cred_file" ]]; then
    local mode
    mode=$(stat -c '%a' "$cred_file" 2>/dev/null || stat -f '%OLp' "$cred_file" 2>/dev/null || echo '?')
    if [[ "$mode" == "600" || "$mode" == "400" ]]; then
      echo "  [OK] firebase/credentials.json existe (chmod $mode)"
    else
      echo "  [AJUSTAR] firebase/credentials.json existe (chmod $mode) — rode: chmod 600 $cred_file"
      FAILS=$((FAILS + 1))
    fi
  else
    echo "  [AVISO] firebase/credentials.json ausente (push FCM server-side pode falhar)"
  fi

  local sw_file="$WWW_ROOT/$site/public/firebase-messaging-sw.js"
  if [[ "$site" == "$WWW_WEBSITE" ]]; then
    [[ -f "$sw_file" ]] && echo "  [OK] firebase-messaging-sw.js existe" || {
      echo "  [FALTA] firebase-messaging-sw.js — rode: ./fix-firebase-config.sh"
      FAILS=$((FAILS + 1))
    }
  fi
  echo ""
}

echo "==> Verificação de .env em $WWW_ROOT"
echo ""

check_site "$WWW_WEBSITE" "https://arrow.app.br"
check_site "$WWW_STORE" "https://store.arrow.app.br"
check_site "$WWW_ADMIN" "https://admin.arrow.app.br"

echo "Landing ($WWW_LANDING): sem .env necessário."
echo ""

if [[ "$FAILS" -gt 0 ]]; then
  echo "==> Resultado: $FAILS problema(s). Corrija e rode de novo."
  echo "    Produção rápida: ./set-production.sh"
  exit 1
fi

echo "==> Resultado: todos os checks obrigatórios OK."
exit 0
