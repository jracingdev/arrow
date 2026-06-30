#!/usr/bin/env bash
# Aplica rewrite Laravel nos vhosts aaPanel (try_files → index.php).
# Sem isso, nginx retorna 404 mesmo com root /public correto.
#
# Uso: sudo ./fix-laravel-rewrite.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

REWRITE_DIR="/www/server/panel/vhost/rewrite"
LARAVEL_REWRITE='location / {
    try_files $uri $uri/ /index.php?$query_string;
}
'

echo "==> fix-laravel-rewrite — aaPanel"
echo ""

if [[ ! -d "$REWRITE_DIR" ]]; then
  echo "ERRO: $REWRITE_DIR não existe"
  exit 1
fi

for domain in "${LARAVEL_SITES[@]}"; do
  REWRITE_FILE="$REWRITE_DIR/${domain}.conf"

  if [[ -f "$REWRITE_FILE" ]]; then
    cp "$REWRITE_FILE" "${REWRITE_FILE}.bak.$(date +%Y%m%d%H%M%S)"
  fi

  printf '%s\n' "$LARAVEL_REWRITE" > "$REWRITE_FILE"
  echo "  [OK] $REWRITE_FILE"
done

echo ""
if nginx -t 2>/dev/null; then
  nginx -s reload 2>/dev/null || /etc/init.d/nginx reload 2>/dev/null || true
  echo "==> Nginx recarregado."
else
  echo "ERRO: nginx -t falhou — restaure os .bak.*"
  exit 1
fi

echo ""
echo "==> Teste HTTPS local:"
PHP82=/www/server/php/82/bin/php
for domain in "${LARAVEL_SITES[@]}"; do
  code=$(curl -sk -o /dev/null -w '%{http_code}' --resolve "${domain}:443:127.0.0.1" "https://${domain}/" 2>/dev/null || echo "000")
  echo "  $domain → HTTP $code"
done

echo ""
echo "==> Concluído. Teste no navegador."
