#!/usr/bin/env bash
# Corrige .user.ini imutáveis do aaPanel (open_basedir com /public/).
# O aaPanel protege .user.ini com chattr +i — chown falha com "Operation not permitted".
#
# Uso: sudo ./fix-user-ini.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"
FIXED=0

fix_ini() {
  local ini="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"

  [[ -f "$ini" ]] || return 0

  echo "  $ini"

  if lsattr "$ini" 2>/dev/null | grep -q 'i'; then
    chattr -i "$ini" 2>/dev/null || {
      echo "    AVISO: não foi possível remover chattr +i — edite no aaPanel"
      return 1
    }
    echo "    chattr -i OK"
  fi

  if grep -q "${site_root}/public" "$ini" 2>/dev/null; then
    cp "$ini" "${ini}.bak.$(date +%Y%m%d%H%M%S)"
    sed -i "s|${site_root}/public/|${site_root}/|g" "$ini"
    sed -i "s|${site_root}/public:|${site_root}/:|g" "$ini"
    echo "    open_basedir corrigido (removido /public)"
    FIXED=$((FIXED + 1))
  else
    echo "    conteúdo atual:"
    grep -E 'open_basedir|disable' "$ini" 2>/dev/null | sed 's/^/      /' || cat "$ini" | sed 's/^/      /'
  fi

  chown www:www "$ini" 2>/dev/null || true
  chmod 644 "$ini" 2>/dev/null || true
  chattr +i "$ini" 2>/dev/null || true
  echo "    chattr +i restaurado"
}

echo "==> fix-user-ini — aaPanel .user.ini"
echo ""

for domain in "${LARAVEL_SITES[@]}"; do
  echo "---- $domain ----"
  fix_ini "$WWW_ROOT/$domain/.user.ini" "$domain" || true
  fix_ini "$WWW_ROOT/$domain/public/.user.ini" "$domain" || true
  echo ""
done

/etc/init.d/php-fpm-82 restart 2>/dev/null || systemctl restart php-fpm-82 2>/dev/null || true

echo "==> $FIXED arquivo(s) .user.ini corrigido(s)."
echo "    Se ainda 403 nginx: sudo ./diagnose-403.sh"
