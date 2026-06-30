#!/usr/bin/env bash
# Corrige .user.ini imutáveis do aaPanel (open_basedir com /public/).
# O aaPanel protege .user.ini com chattr +i — sed/chown falham sem chattr -i antes.
#
# Uso: sudo ./fix-user-ini.sh
#
# Se chattr -i falhar, use o fallback manual no aaPanel (veja deploy/README.md).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"
WEB_USER="${WEB_USER:-www}"
WEB_GROUP="${WEB_GROUP:-www}"
FIXED=0
CHATTR_FAILED=0

print_chattr_fallback() {
  cat <<'EOF'

================================================================================
FALLBACK MANUAL — chattr -i falhou (arquivo imutável)
================================================================================

Para cada site com erro (admin, store, arrow):

  1. aaPanel → Website → clique no domínio
  2. Aba "PHP" → campo open_basedir
  3. CORRETO:  /www/wwwroot/DOMAIN/:/tmp/
     ERRADO:   /www/wwwroot/DOMAIN/public/:/tmp/
  4. Salvar
  5. App Store → PHP 8.2 → Restart

Ou via SSH (como root), tente remover imutabilidade manualmente:

  lsattr /www/wwwroot/admin.arrow.app.br/public/.user.ini
  chattr -i /www/wwwroot/admin.arrow.app.br/public/.user.ini
  sed -i 's|/public/:/tmp/|/:/tmp/|g' /www/wwwroot/admin.arrow.app.br/public/.user.ini
  chattr +i /www/wwwroot/admin.arrow.app.br/public/.user.ini

================================================================================
EOF
}

unlock_ini() {
  local ini="$1"
  if lsattr "$ini" 2>/dev/null | grep -q '[[:space:]]i[[:space:]]'; then
    if chattr -i "$ini" 2>/dev/null; then
      echo "    chattr -i OK"
      return 0
    fi
    echo "    ERRO: chattr -i falhou em $ini"
    lsattr "$ini" 2>/dev/null | sed 's/^/      /' || true
    CHATTR_FAILED=1
    return 1
  fi
  return 0
}

lock_ini() {
  local ini="$1"
  chown "${WEB_USER}:${WEB_GROUP}" "$ini" 2>/dev/null || true
  chmod 644 "$ini" 2>/dev/null || true
  if chattr +i "$ini" 2>/dev/null; then
    echo "    chattr +i restaurado"
  else
    echo "    AVISO: chattr +i falhou (arquivo editável)"
    CHATTR_FAILED=1
  fi
}

patch_open_basedir_content() {
  local ini="$1"
  local site_root="$2"
  local changed=0

  if grep -qE "${site_root}/public(/|[:])" "$ini" 2>/dev/null; then
    cp "$ini" "${ini}.bak.$(date +%Y%m%d%H%M%S)"
    sed -i "s|${site_root}/public/|${site_root}/|g" "$ini"
    sed -i "s|${site_root}/public:|${site_root}/:|g" "$ini"
    # aaPanel às vezes grava sem barra final antes de :/tmp/
    sed -i "s|${site_root}/public/:/tmp/|${site_root}/:/tmp/|g" "$ini"
    changed=1
  fi

  echo "$changed"
}

fix_ini() {
  local ini="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"

  [[ -f "$ini" ]] || return 0

  echo "  $ini"

  if ! unlock_ini "$ini"; then
    echo "    pulando edição — use fallback manual"
    return 1
  fi

  local changed
  changed="$(patch_open_basedir_content "$ini" "$site_root")"

  if [[ "$changed" == "1" ]]; then
    echo "    open_basedir corrigido (removido /public)"
    FIXED=$((FIXED + 1))
  else
    echo "    conteúdo atual:"
    grep -E 'open_basedir|disable' "$ini" 2>/dev/null | sed 's/^/      /' \
      || cat "$ini" | sed 's/^/      /'
  fi

  lock_ini "$ini"
}

echo "==> fix-user-ini — aaPanel .user.ini"
echo "    Raiz esperada: $WWW_ROOT/DOMAIN/ (SEM /public)"
echo ""

for domain in "${LARAVEL_SITES[@]}"; do
  echo "---- $domain ----"
  fix_ini "$WWW_ROOT/$domain/.user.ini" "$domain" || true
  fix_ini "$WWW_ROOT/$domain/public/.user.ini" "$domain" || true
  echo ""
done

/etc/init.d/php-fpm-82 restart 2>/dev/null \
  || systemctl restart php-fpm-82 2>/dev/null \
  || bt restart php-fpm-82 2>/dev/null \
  || true

echo "==> $FIXED arquivo(s) .user.ini corrigido(s)."
echo ""
echo "==> open_basedir atual (.user.ini):"
for domain in "${LARAVEL_SITES[@]}"; do
  echo "---- $domain ----"
  for ini in "$WWW_ROOT/$domain/.user.ini" "$WWW_ROOT/$domain/public/.user.ini"; do
    [[ -f "$ini" ]] || continue
    echo "  $ini"
    grep -E 'open_basedir' "$ini" 2>/dev/null | sed 's/^/    /' || echo "    (sem open_basedir)"
  done
done

if [[ "$CHATTR_FAILED" -gt 0 ]]; then
  echo ""
  print_chattr_fallback
fi

echo ""
echo "==> Se ainda houver erro PHP, rode também: sudo ./fix-open-basedir.sh"
