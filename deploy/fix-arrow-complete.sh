#!/usr/bin/env bash
# Correção focada Arrow — nginx root + open_basedir + permissões + composer.
# Uso: sudo ./fix-arrow-complete.sh [WWW_ROOT]
#
# Ordem:
#   1. fix-nginx-root.sh
#   2. fix-user-ini.sh
#   3. fix-permissions.sh
#   4. post-deploy.sh (somente se vendor/autoload.php ausente em algum site)
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"

if [[ -x /www/server/php/82/bin/php ]]; then
  export PHP_BIN="/www/server/php/82/bin/php"
else
  export PHP_BIN="${PHP_BIN:-php}"
fi

need_composer=0
for domain in "${LARAVEL_SITES[@]}"; do
  if [[ ! -f "$WWW_ROOT/$domain/vendor/autoload.php" ]]; then
    need_composer=1
    echo "AVISO: vendor ausente em $WWW_ROOT/$domain"
  fi
done

echo "========================================"
echo " Arrow — fix-arrow-complete"
echo "========================================"
echo " WWW_ROOT: $WWW_ROOT"
echo " PHP:      $PHP_BIN"
echo " composer: $([[ $need_composer -eq 1 ]] && echo 'SIM (vendor ausente)' || echo 'não necessário')"
echo ""

run_step() {
  local n="$1"
  local name="$2"
  shift 2
  echo ""
  echo ">>> [$n] $name"
  echo "----------------------------------------"
  "$@"
}

run_step 1 "Document root nginx → /public" \
  bash "$SCRIPT_DIR/fix-nginx-root.sh"

run_step 2 "open_basedir .user.ini (raiz do site)" \
  bash "$SCRIPT_DIR/fix-user-ini.sh"

run_step 3 "Permissões www:www" \
  bash "$SCRIPT_DIR/fix-permissions.sh" "$WWW_ROOT"

if [[ "$need_composer" -eq 1 ]]; then
  run_step 4 "Composer + cache Laravel (vendor ausente)" \
    bash "$SCRIPT_DIR/post-deploy.sh" "$WWW_ROOT"
else
  echo ""
  echo ">>> [4] post-deploy — pulado (vendor OK em todos os sites)"
fi

echo ""
echo "========================================"
echo " Verificação final"
echo "========================================"
echo ""
echo "nginx -T | grep root:"
nginx -T 2>/dev/null | grep -E 'root.*/www/wwwroot/(arrow|admin|store)\.arrow' \
  | sed 's/^/  /' || echo "  (nenhuma linha — rode como root)"

echo ""
for domain in "${LARAVEL_SITES[@]}"; do
  code=$(curl -sI -o /dev/null -w '%{http_code}' -H "Host: $domain" "http://127.0.0.1/" 2>/dev/null || echo "?")
  echo "  curl Host:$domain → HTTP $code"
done

echo ""
echo "========================================"
echo " fix-arrow-complete concluído"
echo "========================================"
echo ""
echo "Teste no navegador:"
echo "  https://arrow.app.br"
echo "  https://store.arrow.app.br"
echo "  https://admin.arrow.app.br"
echo ""
echo "Se arrow.app.br ainda falhar: sudo ./diagnose-all.sh | tee /tmp/diagnose-all.log"
