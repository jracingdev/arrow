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

verify_firebase_config_cached() {
  local site_path="$1"
  local site_name="$2"

  if [[ ! -f "$site_path/artisan" ]]; then
    return 0
  fi

  local api_key
  api_key="$(
    cd "$site_path" && $PHP_BIN artisan tinker --execute="echo config('firebase.api_key');" 2>/dev/null \
      | tr -d '\r' | tail -n 1
  )"

  if [[ -n "$api_key" && "$api_key" != "null" ]]; then
    echo "  [OK] config('firebase.api_key') disponível após config:cache"
  else
    echo "  [FALHA] config('firebase.api_key') vazio — confira config/firebase.php e .env"
    return 1
  fi
}

verify_firebase_inline_config() {
  local url="$1"
  local label="$2"

  if ! command -v curl >/dev/null 2>&1; then
    echo "  [SKIP] curl não disponível para verificar $label"
    return 0
  fi

  local body
  body="$(curl -fsSL --max-time 15 "$url" 2>/dev/null || true)"

  if [[ -z "$body" ]]; then
    echo "  [AVISO] Não foi possível obter HTML de $url"
    return 0
  fi

  if echo "$body" | grep -q '__firebaseConfig'; then
    echo "  [OK] $label expõe window.__firebaseConfig no HTML"
  else
    echo "  [AVISO] $label não contém __firebaseConfig (página pode não carregar Firebase)"
  fi

  if echo "$body" | grep -q '"apiKey":""'; then
    echo "  [FALHA] $label tem apiKey vazio no HTML — FIREBASE_APIKEY não chegou ao config cache"
    return 1
  fi

  if echo "$body" | grep -qE '"apiKey":"[^"]{10,}"'; then
    echo "  [OK] $label tem apiKey preenchido no HTML"
  fi
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
    verify_firebase_config_cached "$site_path" "$site" || true
    echo ""
  fi
done

echo "==> Verificando __firebaseConfig no HTML (requer site no ar)"
verify_firebase_inline_config "https://${WWW_WEBSITE}/login" "$WWW_WEBSITE/login" || true
verify_firebase_inline_config "https://${WWW_ADMIN}/login" "$WWW_ADMIN/login" || true
verify_firebase_inline_config "https://${WWW_STORE}/login" "$WWW_STORE/login" || true
echo ""

echo "==> fix-firebase-config concluído."
if [[ "$any_missing" -eq 0 ]]; then
  echo "    Todas as variáveis FIREBASE_* estão preenchidas."
else
  echo "    Corrija os .env e rode novamente: sudo ./fix-firebase-config.sh"
fi
