#!/usr/bin/env bash
# Corrige open_basedir nos vhosts aaPanel para Laravel.
# Laravel precisa de vendor/, storage/, bootstrap/ na raiz do site — NÃO só public/.
#
# Uso: sudo ./fix-open-basedir.sh
#
# Complementa fix-user-ini.sh (que corrige .user.ini imutáveis).
# Para correção completa: sudo ./fix-open-basedir-now.sh
#
# Correção manual no aaPanel (se este script não encontrar os arquivos):
#   Website → selecione o site → PHP → open_basedir
#   Valor correto: /www/wwwroot/DOMAIN/:/tmp/
#   Valor ERRADO:  /www/wwwroot/DOMAIN/public/:/tmp/
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"

VHOST_DIRS=(
  "/www/server/panel/vhost/nginx"
  "/www/server/nginx/conf/vhost"
  "/www/server/panel/vhost/php"
  "/www/server/panel/vhost/open_basedir"
)

PHP_FPM_POOL_DIR="/www/server/php/82/etc/php-fpm.d"

DOMAINS=("${LARAVEL_SITES[@]}")
PATCHED=0

print_manual_fix() {
  cat <<'EOF'

================================================================================
CORREÇÃO MANUAL NO aaPanel (se o patch automático não aplicou)
================================================================================

Para cada site Laravel (arrow.app.br, store.arrow.app.br, admin.arrow.app.br):

  1. aaPanel → Website → clique no domínio
  2. Aba "PHP" (ou "Site directory" → PHP settings)
  3. Campo "open_basedir":
     - CORRETO:  /www/wwwroot/SEU_DOMINIO/:/tmp/
     - ERRADO:   /www/wwwroot/SEU_DOMINIO/public/:/tmp/
  4. Alternativa: desabilitar a restrição open_basedir
  5. Salvar e recarregar PHP-FPM (App Store → PHP 8.2 → Restart)

Sintoma no log PHP / tela branca:
  require(.../vendor/autoload.php): open_basedir restriction in effect
  file_put_contents(.../storage/...): open_basedir restriction in effect

================================================================================
EOF
}

apply_sed_fix() {
  local file="$1"
  local site_root="$2"
  local wrong="${site_root}/public/"
  local right="${site_root}/"

  if ! grep -qE "${site_root}/public(/|[:])" "$file" 2>/dev/null; then
    return 1
  fi

  cp "$file" "${file}.bak.$(date +%Y%m%d%H%M%S)"
  sed -i "s|${wrong}|${right}|g" "$file"
  sed -i "s|${site_root}/public:|${site_root}/:|g" "$file"
  sed -i "s|${site_root}/public/:/tmp/|${site_root}/:/tmp/|g" "$file"
  echo "  [PATCH] $file"
  echo "          ${wrong} → ${right}"
  PATCHED=$((PATCHED + 1))
  return 0
}

patch_file_for_domain() {
  local file="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"

  [[ -f "$file" ]] || return 1

  # Arquivos nomeados pelo domínio (ex.: open_basedir/admin.arrow.app.br.conf)
  if [[ "$(basename "$file")" == *"$domain"* ]]; then
    apply_sed_fix "$file" "$site_root" && return 0
    return 1
  fi

  if ! grep -q "$domain" "$file" 2>/dev/null \
    && ! grep -q "open_basedir.*${site_root}" "$file" 2>/dev/null; then
    return 1
  fi

  apply_sed_fix "$file" "$site_root"
}

patch_domain_open_basedir_files() {
  local domain="$1"
  local site_root="$WWW_ROOT/$domain"
  local ob_dir="/www/server/panel/vhost/open_basedir"

  [[ -d "$ob_dir" ]] || return 0

  local f
  for f in "$ob_dir/$domain.conf" "$ob_dir/${domain}.conf" "$ob_dir/$domain"; do
    [[ -f "$f" ]] && apply_sed_fix "$f" "$site_root" || true
  done

  while IFS= read -r -d '' f; do
    [[ "$(basename "$f")" == *"$domain"* ]] && apply_sed_fix "$f" "$site_root" || true
  done < <(find "$ob_dir" -maxdepth 1 -type f -print0 2>/dev/null)
}

patch_php_fpm_pool() {
  local domain="$1"
  local site_root="$WWW_ROOT/$domain"

  [[ -d "$PHP_FPM_POOL_DIR" ]] || return 0

  local pool
  for pool in "$PHP_FPM_POOL_DIR"/*.conf; do
    [[ -f "$pool" ]] || continue
    if grep -q "$domain" "$pool" 2>/dev/null || grep -q "$site_root" "$pool" 2>/dev/null; then
      apply_sed_fix "$pool" "$site_root" || true
    fi
  done
}

echo "==> fix-open-basedir — Laravel no aaPanel"
echo "    Sites: ${DOMAINS[*]}"
echo "    Raiz esperada: $WWW_ROOT/DOMAIN/ (SEM /public)"
echo ""

for domain in "${DOMAINS[@]}"; do
  echo "==> Arquivos open_basedir dedicados: $domain"
  patch_domain_open_basedir_files "$domain"
done

for dir in "${VHOST_DIRS[@]}"; do
  [[ -d "$dir" ]] || continue
  echo "==> Procurando em $dir ..."
  while IFS= read -r -d '' file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(find "$dir" -maxdepth 2 -type f \( -name '*.conf' -o -name '*.vhost' \) -print0 2>/dev/null)
done

if [[ -d /www/server/panel/vhost ]]; then
  echo "==> Grep adicional em /www/server/panel/vhost ..."
  while IFS= read -r file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(grep -rlE 'open_basedir.*/public' /www/server/panel/vhost 2>/dev/null || true)
fi

for domain in "${DOMAINS[@]}"; do
  patch_php_fpm_pool "$domain"
done

echo ""
if [[ "$PATCHED" -gt 0 ]]; then
  echo "==> $PATCHED arquivo(s) corrigido(s). Recarregando serviços ..."
  if nginx -t 2>/dev/null; then
    nginx -s reload 2>/dev/null || /etc/init.d/nginx reload 2>/dev/null || bt reload nginx 2>/dev/null || true
    echo "    Nginx recarregado."
  else
    echo "    AVISO: nginx -t falhou — restaure o .bak.* se necessário."
  fi
  /etc/init.d/php-fpm-82 restart 2>/dev/null \
    || systemctl restart php-fpm-82 2>/dev/null \
    || bt restart php-fpm-82 2>/dev/null \
    || true
  echo "    PHP-FPM 8.2 reiniciado."
else
  echo "==> Nenhum vhost patchado automaticamente."
  print_manual_fix
fi

echo ""
echo "==> open_basedir atual (grep nos vhosts + .user.ini):"
for domain in "${DOMAINS[@]}"; do
  echo "---- $domain ----"
  grep -rh "open_basedir" /www/server/panel/vhost/ /www/server/nginx/conf/vhost/ 2>/dev/null \
    | grep "$domain" \
    | sed 's/^[[:space:]]*/    /' || echo "    (nenhuma linha vhost — confira .user.ini)"
  for ini in "$WWW_ROOT/$domain/.user.ini" "$WWW_ROOT/$domain/public/.user.ini"; do
    [[ -f "$ini" ]] && grep 'open_basedir' "$ini" 2>/dev/null | sed "s|^|    $ini: |" || true
  done
done

echo ""
echo "==> Concluído."
