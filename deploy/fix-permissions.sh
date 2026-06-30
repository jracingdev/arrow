#!/usr/bin/env bash
# Corrige permissões para Laravel no aaPanel (403 nginx por traverse/read).
# Uso: sudo ./fix-permissions.sh [WWW_ROOT]
#
# Após deploy com sudo, pastas podem ficar root:root 700 — nginx (www) não
# consegue atravessar até public/index.php → 403 Forbidden plain nginx.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-${WWW_ROOT:-/www/wwwroot}}"
WEB_USER="${WEB_USER:-www}"
WEB_GROUP="${WEB_GROUP:-www}"

echo "==> fix-permissions — Laravel no aaPanel"
echo "    WWW_ROOT: $WWW_ROOT"
echo "    Owner: ${WEB_USER}:${WEB_GROUP}"
echo "    Sites: ${LARAVEL_SITES[*]}"
echo ""

fix_site() {
  local site="$1"
  local path="$WWW_ROOT/$site"

  if [[ ! -d "$path" ]]; then
    echo "AVISO: $path não existe — pulando."
    return 0
  fi

  echo "---- $site ----"

  # .user.ini do aaPanel é imutável (chattr +i) — ajustar antes do chown
  for ini in "$path/.user.ini" "$path/public/.user.ini"; do
    if [[ -f "$ini" ]] && lsattr "$ini" 2>/dev/null | grep -q 'i'; then
      chattr -i "$ini" 2>/dev/null || true
    fi
  done

  chown -R "${WEB_USER}:${WEB_GROUP}" "$path" 2>/dev/null || {
    chown -R "${WEB_USER}:${WEB_GROUP}" "$path/public" "$path/vendor" "$path/storage" "$path/bootstrap" "$path/app" "$path/config" "$path/database" "$path/resources" "$path/routes" 2>/dev/null || true
  }
  for ini in "$path/.user.ini" "$path/public/.user.ini"; do
    if [[ -f "$ini" ]]; then
      chown "${WEB_USER}:${WEB_GROUP}" "$ini" 2>/dev/null || true
      chattr +i "$ini" 2>/dev/null || true
    fi
  done

  echo "    chown ${WEB_USER}:${WEB_GROUP} OK"

  find "$path" -type d -exec chmod 755 {} +
  find "$path" -type f -exec chmod 644 {} +
  echo "    dirs 755, files 644 OK"

  if [[ -d "$path/storage" ]]; then
    chmod -R 775 "$path/storage"
    chown -R "${WEB_USER}:${WEB_GROUP}" "$path/storage"
    echo "    storage/ 775 OK"
  fi

  if [[ -d "$path/bootstrap/cache" ]]; then
    chmod -R 775 "$path/bootstrap/cache"
    chown -R "${WEB_USER}:${WEB_GROUP}" "$path/bootstrap/cache"
    echo "    bootstrap/cache/ 775 OK"
  fi

  if [[ -f "$path/public/index.php" ]]; then
    chmod 644 "$path/public/index.php"
    chown "${WEB_USER}:${WEB_GROUP}" "$path/public/index.php"
    if sudo -u "$WEB_USER" test -r "$path/public/index.php" 2>/dev/null; then
      echo "    public/index.php legível por $WEB_USER OK"
    else
      echo "    AVISO: $WEB_USER ainda não lê public/index.php — verifique SELinux/AppArmor"
    fi
  else
    echo "    ERRO: public/index.php ausente — rode deploy.sh primeiro"
  fi

  if [[ -d "$path/public" ]]; then
    chmod 755 "$path/public"
  fi

  echo ""
}

# Garantir traverse em /www e /www/wwwroot
for parent in /www /www/wwwroot; do
  if [[ -d "$parent" ]]; then
    chmod 755 "$parent" 2>/dev/null || true
    echo "==> $parent → 755"
  fi
done
echo ""

for site in "${LARAVEL_SITES[@]}"; do
  fix_site "$site"
done

echo "==> fix-permissions concluído."
echo "    Teste: curl -I https://store.arrow.app.br"
echo "    Se ainda 403: sudo ./diagnose-403.sh e confira root no vhost (deve ser .../public)."
