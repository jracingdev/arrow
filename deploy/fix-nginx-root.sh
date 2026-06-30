#!/usr/bin/env bash
# Corrige document root nos vhosts aaPanel para Laravel.
# Laravel exige root apontando para public/ — não a raiz do site.
#
# Uso: sudo ./fix-nginx-root.sh
#
# Sintoma típico: HTTP 403 Forbidden (página plain nginx, sem PHP)
#   root /www/wwwroot/DOMAIN;          ← ERRADO
#   root /www/wwwroot/DOMAIN/public;   ← CORRETO
#
# Correção manual imediata (um domínio):
#   sudo sed -i 's|root /www/wwwroot/admin.arrow.app.br;|root /www/wwwroot/admin.arrow.app.br/public;|' \
#     /www/server/panel/vhost/nginx/admin.arrow.app.br.conf
#   sudo nginx -t && sudo nginx -s reload
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"

VHOST_DIR="/www/server/panel/vhost/nginx"
EXTENSION_BASE="${VHOST_DIR}/extension"
ALT_VHOST_DIR="/www/server/nginx/conf/vhost"

DOMAINS=("${LARAVEL_SITES[@]}")
PATCHED=0

print_manual_fix() {
  cat <<'EOF'

================================================================================
CORREÇÃO MANUAL — document root nginx (Laravel)
================================================================================

Para cada site Laravel (arrow.app.br, store.arrow.app.br, admin.arrow.app.br):

  1. aaPanel → Website → clique no domínio
  2. Site directory → Running directory = /public
  3. Ou edite o vhost e confirme:
       root /www/wwwroot/DOMAIN/public;
     (NÃO use root /www/wwwroot/DOMAIN; sem /public)

Comandos sed imediatos (aaPanel padrão):

  sudo sed -i 's|root /www/wwwroot/arrow.app.br;|root /www/wwwroot/arrow.app.br/public;|' \
    /www/server/panel/vhost/nginx/arrow.app.br.conf
  sudo sed -i 's|root /www/wwwroot/store.arrow.app.br;|root /www/wwwroot/store.arrow.app.br/public;|' \
    /www/server/panel/vhost/nginx/store.arrow.app.br.conf
  sudo sed -i 's|root /www/wwwroot/admin.arrow.app.br;|root /www/wwwroot/admin.arrow.app.br/public;|' \
    /www/server/panel/vhost/nginx/admin.arrow.app.br.conf
  sudo nginx -t && sudo nginx -s reload

Sintoma: 403 Forbidden com <center>nginx</center> — nginx não encontra index.php
na raiz do site (só existe em public/).

================================================================================
EOF
}

collect_vhost_files() {
  local domain="$1"
  local -n _out="$2"

  local main_conf="${VHOST_DIR}/${domain}.conf"
  [[ -f "$main_conf" ]] && _out+=("$main_conf")

  local ext_dir="${EXTENSION_BASE}/${domain}"
  if [[ -d "$ext_dir" ]]; then
    while IFS= read -r -d '' f; do
      _out+=("$f")
    done < <(find "$ext_dir" -maxdepth 1 -type f -name '*.conf' -print0 2>/dev/null)
  fi

  if [[ -d "$ALT_VHOST_DIR" ]]; then
    local alt_conf="${ALT_VHOST_DIR}/${domain}.conf"
    [[ -f "$alt_conf" ]] && _out+=("$alt_conf")
  fi
}

patch_file_for_domain() {
  local file="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"
  local wrong="root ${site_root};"
  local right="root ${site_root}/public;"

  if [[ ! -f "$file" ]]; then
    return 1
  fi

  if ! grep -q "$domain" "$file" 2>/dev/null && ! grep -q "root[[:space:]]*${site_root}" "$file" 2>/dev/null; then
    return 1
  fi

  if grep -qE "root[[:space:]]+${site_root}/public[[:space:]]*;" "$file" 2>/dev/null; then
    return 1
  fi

  if ! grep -qE "root[[:space:]]+${site_root}[[:space:]]*;" "$file" 2>/dev/null; then
    return 1
  fi

  cp "$file" "${file}.bak.$(date +%Y%m%d%H%M%S)"
  sed -i "s|root ${site_root};|root ${site_root}/public;|g" "$file"
  echo "  [PATCH] $file"
  echo "          root ${site_root}; → root ${site_root}/public;"
  PATCHED=$((PATCHED + 1))
  return 0
}

echo "==> fix-nginx-root — document root Laravel (/public)"
echo "    Sites: ${DOMAINS[*]}"
echo "    Raiz esperada no vhost: $WWW_ROOT/DOMAIN/public"
echo ""

for domain in "${DOMAINS[@]}"; do
  echo "==> $domain"
  files=()
  collect_vhost_files "$domain" files

  if [[ ${#files[@]} -eq 0 ]]; then
    echo "  AVISO: nenhum vhost encontrado (esperado: ${VHOST_DIR}/${domain}.conf)"
    continue
  fi

  domain_patched=0
  for file in "${files[@]}"; do
    if patch_file_for_domain "$file" "$domain"; then
      domain_patched=$((domain_patched + 1))
    else
      if grep -qE "root[[:space:]]+${WWW_ROOT}/${domain}/public[[:space:]]*;" "$file" 2>/dev/null; then
        echo "  [OK] $file (já aponta para /public)"
      fi
    fi
  done

  if [[ "$domain_patched" -eq 0 ]]; then
    echo "  Nenhum patch necessário ou root ainda incorreto — confira manualmente."
  fi
  echo ""
done

# Busca adicional: qualquer .conf em vhost/nginx que cite o domínio
if [[ -d "$VHOST_DIR" ]]; then
  while IFS= read -r -d '' file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(find "$VHOST_DIR" -maxdepth 2 -type f -name '*.conf' -print0 2>/dev/null)
fi

echo ""
if [[ "$PATCHED" -gt 0 ]]; then
  echo "==> $PATCHED arquivo(s) corrigido(s). Testando nginx ..."
  if nginx -t 2>/dev/null; then
    nginx -s reload 2>/dev/null || /etc/init.d/nginx reload 2>/dev/null || bt reload nginx 2>/dev/null || true
    echo "    Nginx recarregado."
  else
    echo "    ERRO: nginx -t falhou — restaure os arquivos .bak.* e corrija manualmente."
    exit 1
  fi
else
  echo "==> Nenhum vhost patchado automaticamente."
  print_manual_fix
fi

echo ""
echo "==> root atual (nginx -T | grep root):"
for domain in "${DOMAINS[@]}"; do
  echo "---- $domain ----"
  nginx -T 2>/dev/null | grep -E "root.*/www/wwwroot/${domain}" | sed 's/^[[:space:]]*/    /' \
    || echo "    (nenhuma linha — rode: sudo nginx -T | grep ${domain})"
done

echo ""
echo "==> Concluído."
