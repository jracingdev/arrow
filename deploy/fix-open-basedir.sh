#!/usr/bin/env bash
# Corrige open_basedir nos vhosts aaPanel para Laravel.
# Laravel precisa de vendor/, storage/, bootstrap/ na raiz do site — NÃO só public/.
#
# Uso: sudo ./fix-open-basedir.sh
#
# Correção manual no aaPanel (se este script não encontrar os arquivos):
#   Website → selecione o site → PHP → open_basedir
#   Valor correto: /www/wwwroot/DOMAIN/:/tmp/
#   Valor ERRADO:  /www/wwwroot/DOMAIN/public/:/tmp/
#   Ou desmarque "Restringir open_basedir" se preferir.
#
# Sintoma típico: vendor/autoload.php: open_basedir restriction in effect
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

patch_file_for_domain() {
  local file="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"
  local wrong="${site_root}/public/"
  local right="${site_root}/"

  if [[ ! -f "$file" ]]; then
    return 1
  fi

  if ! grep -q "$domain" "$file" 2>/dev/null && ! grep -q "open_basedir.*${site_root}" "$file" 2>/dev/null; then
    return 1
  fi

  if ! grep -q "${wrong}" "$file" 2>/dev/null; then
    return 1
  fi

  cp "$file" "${file}.bak.$(date +%Y%m%d%H%M%S)"
  sed -i "s|${wrong}|${right}|g" "$file"
  echo "  [PATCH] $file"
  echo "          ${wrong} → ${right}"
  PATCHED=$((PATCHED + 1))
  return 0
}

echo "==> fix-open-basedir — Laravel no aaPanel"
echo "    Sites: ${DOMAINS[*]}"
echo "    Raiz esperada: $WWW_ROOT/DOMAIN/ (SEM /public)"
echo ""

for dir in "${VHOST_DIRS[@]}"; do
  [[ -d "$dir" ]] || continue
  echo "==> Procurando em $dir ..."
  while IFS= read -r -d '' file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(find "$dir" -maxdepth 2 -type f \( -name '*.conf' -o -name '*.vhost' \) -print0 2>/dev/null)
done

# grep adicional em todo vhost panel
if [[ -d /www/server/panel/vhost ]]; then
  while IFS= read -r file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(grep -rl 'open_basedir' /www/server/panel/vhost 2>/dev/null || true)
fi

echo ""
if [[ "$PATCHED" -gt 0 ]]; then
  echo "==> $PATCHED arquivo(s) corrigido(s). Recarregando serviços ..."
  if nginx -t 2>/dev/null; then
    nginx -s reload 2>/dev/null || /etc/init.d/nginx reload 2>/dev/null || bt reload nginx 2>/dev/null || true
    echo "    Nginx recarregado."
  else
    echo "    AVISO: nginx -t falhou — restaure o .bak.* se necessário."
  fi
  /etc/init.d/php-fpm-82 restart 2>/dev/null || systemctl restart php-fpm-82 2>/dev/null || bt restart php-fpm-82 2>/dev/null || true
  echo "    PHP-FPM 8.2 reiniciado."
else
  echo "==> Nenhum vhost patchado automaticamente."
  print_manual_fix
fi

echo ""
echo "==> open_basedir atual (grep nos vhosts):"
for domain in "${DOMAINS[@]}"; do
  echo "---- $domain ----"
  grep -rh "open_basedir" /www/server/panel/vhost/ /www/server/nginx/conf/vhost/ 2>/dev/null \
    | grep "$domain" \
    | sed 's/^[[:space:]]*/    /' || echo "    (nenhuma linha — confira no painel aaPanel)"
done

echo ""
echo "==> Concluído."
