#!/usr/bin/env bash
# Valida variáveis FIREBASE_* nos .env Laravel, gera firebase-messaging-sw.js
# e limpa/recria o cache de configuração.
#
# Uso: sudo ./fix-firebase-config.sh [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"
TEMPLATE="$SCRIPT_DIR/templates/firebase-messaging-sw.js.template"

FIREBASE_VARS=(
  FIREBASE_APIKEY
  FIREBASE_AUTH_DOMAIN
  FIREBASE_DATABASE_URL
  FIREBASE_PROJECT_ID
  FIREBASE_STORAGE_BUCKET
  FIREBASE_MESSAAGING_SENDER_ID
  FIREBASE_APP_ID
  FIREBASE_MEASUREMENT_ID
)

if [[ -z "${PHP_BIN:-}" ]] && [[ -x /www/server/php/82/bin/php ]]; then
  PHP_BIN="/www/server/php/82/bin/php"
else
  PHP_BIN="${PHP_BIN:-php}"
fi

read_env_var() {
  local env_file="$1"
  local key="$2"
  grep "^${key}=" "$env_file" 2>/dev/null | cut -d= -f2- | tr -d '"' | tr -d "'"
}

check_firebase_env() {
  local site="$1"
  local env_file="$WWW_ROOT/$site/.env"
  local missing=0

  echo "---- $site ----"

  if [[ ! -f "$env_file" ]]; then
    echo "  [FALTA] .env não existe"
    return 1
  fi

  for var in "${FIREBASE_VARS[@]}"; do
    local val
    val="$(read_env_var "$env_file" "$var")"
    if [[ -n "$val" ]]; then
      echo "  [OK] $var"
    else
      echo "  [FALTA] $var"
      missing=1
    fi
  done

  return "$missing"
}

generate_messaging_sw() {
  local env_file="$1"
  local out_file="$2"

  if [[ ! -f "$TEMPLATE" ]]; then
    echo "  ERRO: template ausente: $TEMPLATE"
    return 1
  fi

  local content
  content="$(cat "$TEMPLATE")"

  for var in "${FIREBASE_VARS[@]}"; do
    local val
    val="$(read_env_var "$env_file" "$var")"
    content="${content//\{\{$var\}\}/$val}"
  done

  mkdir -p "$(dirname "$out_file")"
  printf '%s\n' "$content" > "$out_file"
  echo "  [OK] Gerado: $out_file"
}

clear_laravel_config_cache() {
  local site_path="$1"

  if [[ ! -f "$site_path/artisan" ]]; then
    return 0
  fi

  cd "$site_path"
  $PHP_BIN artisan config:clear
  $PHP_BIN artisan config:cache
  echo "  [OK] Cache de config recriado"
}

echo "==> Firebase — validação e geração de firebase-messaging-sw.js"
echo "    WWW_ROOT: $WWW_ROOT"
echo ""

any_missing=0
for site in "${LARAVEL_SITES[@]}"; do
  if ! check_firebase_env "$site"; then
    any_missing=1
  fi
  echo ""
done

if [[ "$any_missing" -eq 1 ]]; then
  echo "AVISO: Variáveis FIREBASE_* incompletas em um ou mais painéis."
  echo "       Preencha o .env conforme deploy/README.md → seção Firebase."
  echo "       O site pode exibir erros no console até todas estarem definidas."
  echo ""
fi

website_env="$WWW_ROOT/$WWW_WEBSITE/.env"
website_sw="$WWW_ROOT/$WWW_WEBSITE/public/firebase-messaging-sw.js"
repo_sw="$SCRIPT_DIR/../web/website/public/firebase-messaging-sw.js"

if [[ -f "$website_env" ]]; then
  echo "==> Gerando firebase-messaging-sw.js (website)"
  generate_messaging_sw "$website_env" "$website_sw"
  if [[ -d "$(dirname "$repo_sw")" ]]; then
    generate_messaging_sw "$website_env" "$repo_sw" || true
  fi
  echo ""
fi

echo "==> Limpando cache Laravel (config)"
for site in "${LARAVEL_SITES[@]}"; do
  site_path="$WWW_ROOT/$site"
  if [[ -f "$site_path/.env" ]]; then
    echo "---- $site ----"
    clear_laravel_config_cache "$site_path"
    echo ""
  fi
done

echo "==> fix-firebase-config concluído."
if [[ "$any_missing" -eq 0 ]]; then
  echo "    Todas as variáveis FIREBASE_* estão preenchidas."
else
  echo "    Corrija os .env e rode novamente: sudo ./fix-firebase-config.sh"
fi
