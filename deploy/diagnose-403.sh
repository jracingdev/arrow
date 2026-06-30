#!/usr/bin/env bash
# Diagnóstico de HTTP 403 nginx (página plain nginx, sem PHP).
# Uso: sudo ./diagnose-403.sh
#
# Causas comuns no aaPanel + Laravel:
#   - Document root aponta para raiz do site (sem index) em vez de /public
#   - Permissões: www não consegue atravessar pastas pai até public/
#   - public/index.php ausente ou não legível (644)
#   - Deploy com sudo deixou pastas 700 root:root
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"
NGINX_USER="${NGINX_USER:-www}"
PHP_VERSION="${PHP_VERSION:-82}"

DOMAINS=("${LARAVEL_SITES[@]}")
VHOST_DIRS=(
  "/www/server/panel/vhost/nginx"
  "/www/server/nginx/conf/vhost"
)

section() {
  echo ""
  echo "================================================================================"
  echo " $1"
  echo "================================================================================"
}

check_traverse() {
  local path="$1"
  local p="$path"
  while [[ "$p" != "/" && -n "$p" ]]; do
    if [[ ! -e "$p" ]]; then
      echo "    FALTA: $p"
      return 1
    fi
    local perms
    perms=$(stat -c '%a %U:%G' "$p" 2>/dev/null || stat -f '%OLp %Su:%Sg' "$p" 2>/dev/null || echo "?")
    local other_x
    other_x=$(stat -c '%a' "$p" 2>/dev/null | tail -c 2 | head -c 1 || echo "?")
    if [[ "$other_x" == "0" && "$p" != "$path" ]]; then
      echo "    AVISO: sem execute para outros em $p ($perms) — nginx pode não atravessar"
    fi
    p=$(dirname "$p")
  done
  return 0
}

test_www_read() {
  local file="$1"
  if id "$NGINX_USER" &>/dev/null; then
    if sudo -u "$NGINX_USER" test -r "$file" 2>/dev/null; then
      echo "    OK: usuário $NGINX_USER consegue ler $file"
    else
      echo "    FALHA: usuário $NGINX_USER NÃO consegue ler $file"
    fi
    local dir
    dir=$(dirname "$file")
    if sudo -u "$NGINX_USER" test -x "$dir" 2>/dev/null; then
      echo "    OK: usuário $NGINX_USER consegue entrar em $dir"
    else
      echo "    FALHA: usuário $NGINX_USER NÃO consegue entrar (execute) em $dir"
    fi
  else
    echo "    AVISO: usuário $NGINX_USER não existe — pulando teste sudo -u"
  fi
}

grep_vhost_for_domain() {
  local domain="$1"
  local found=0
  for dir in "${VHOST_DIRS[@]}"; do
    [[ -d "$dir" ]] || continue
    while IFS= read -r -d '' conf; do
      if grep -q "$domain" "$conf" 2>/dev/null; then
        echo "    --- $conf ---"
        grep -E '^\s*(root|index|server_name|open_basedir|fastcgi_param.*open_basedir|deny|autoindex)' "$conf" 2>/dev/null \
          | sed 's/^[[:space:]]*/      /' || true
        found=1
      fi
    done < <(find "$dir" -maxdepth 1 -type f -name '*.conf' -print0 2>/dev/null)
  done
  if [[ "$found" -eq 0 ]]; then
    echo "    (nenhum vhost encontrado em ${VHOST_DIRS[*]})"
  fi
}

echo "==> diagnose-403 — Arrow Laravel (aaPanel)"
echo "    WWW_ROOT: $WWW_ROOT"
echo "    nginx user: $NGINX_USER"
echo "    Sites: ${DOMAINS[*]}"
echo "    Data: $(date -Iseconds 2>/dev/null || date)"

section "1. index.php e permissões por site"
for domain in "${DOMAINS[@]}"; do
  SITE="$WWW_ROOT/$domain"
  PUBLIC="$SITE/public"
  INDEX="$PUBLIC/index.php"

  echo ""
  echo "---- $domain ----"
  echo "    Site root: $SITE"

  if [[ ! -d "$SITE" ]]; then
    echo "    ERRO: pasta do site não existe!"
    continue
  fi

  echo "    Permissões site root:"
  ls -lad "$SITE" 2>/dev/null | sed 's/^/      /' || echo "      (ls falhou)"

  if [[ ! -d "$PUBLIC" ]]; then
    echo "    ERRO: $PUBLIC não existe!"
    continue
  fi

  echo "    Permissões public/:"
  ls -lad "$PUBLIC" 2>/dev/null | sed 's/^/      /' || true

  if [[ -f "$INDEX" ]]; then
    echo "    index.php:"
    ls -la "$INDEX" 2>/dev/null | sed 's/^/      /' || true
    test_www_read "$INDEX"
  else
    echo "    ERRO CRÍTICO: $INDEX NÃO EXISTE — rode deploy.sh / fix-all.sh"
  fi

  echo "    Conteúdo public/ (primeiros itens):"
  ls -la "$PUBLIC" 2>/dev/null | head -15 | sed 's/^/      /' || true

  if [[ -d "$SITE/vendor" ]]; then
    echo "    vendor/:"
    ls -lad "$SITE/vendor" 2>/dev/null | sed 's/^/      /' || true
  else
    echo "    AVISO: vendor/ ausente — composer install necessário (403 nginx ≠ vendor, mas Laravel falhará depois)"
  fi

  if [[ -d "$SITE/storage" ]]; then
    echo "    storage/:"
    ls -lad "$SITE/storage" 2>/dev/null | sed 's/^/      /' || true
  fi

  echo "    Traversal path (nginx precisa de +x em cada pasta pai):"
  check_traverse "$PUBLIC" || true
done

section "2. Caminho pai /www e /www/wwwroot"
for p in /www /www/wwwroot; do
  if [[ -d "$p" ]]; then
    ls -lad "$p" | sed 's/^/  /'
    test_www_read "$p" 2>/dev/null || sudo -u "$NGINX_USER" test -x "$p" 2>/dev/null && echo "    OK: $NGINX_USER pode atravessar $p" || echo "    FALHA: $NGINX_USER não atravessa $p"
  else
    echo "  AVISO: $p não existe"
  fi
done

section "3. Nginx vhost — root, index, open_basedir"
for domain in "${DOMAINS[@]}"; do
  echo ""
  echo "---- $domain ----"
  grep_vhost_for_domain "$domain"

  expected_root="$WWW_ROOT/$domain/public"
  echo ""
  echo "    Document root ESPERADO: $expected_root"
  echo "    aaPanel Running directory deve ser: /public"
done

section "4. open_basedir (grep global nos vhosts)"
grep -rh 'open_basedir' /www/server/panel/vhost/ /www/server/nginx/conf/vhost/ 2>/dev/null \
  | grep -E 'arrow\.app\.br|store\.|admin\.' \
  | sed 's/^[[:space:]]*/  /' || echo "  (nenhuma linha encontrada)"

section "5. PHP-FPM pool (PHP $PHP_VERSION)"
POOL_DIR="/www/server/php/${PHP_VERSION}/etc/php-fpm.d"
if [[ -d "$POOL_DIR" ]]; then
  for domain in "${DOMAINS[@]}"; do
    echo ""
    echo "---- $domain ----"
    grep -rl "$domain" "$POOL_DIR" 2>/dev/null | while read -r pool; do
      echo "    Pool: $pool"
      grep -E '^(user|group|listen|open_basedir|php_admin_value\[open_basedir\])' "$pool" 2>/dev/null \
        | sed 's/^/      /' || true
    done
    grep -rl "$WWW_ROOT/$domain" "$POOL_DIR" 2>/dev/null | while read -r pool; do
      echo "    Pool (por path): $pool"
      grep -E 'open_basedir|php_admin_value' "$pool" 2>/dev/null | sed 's/^/      /' || true
    done
  done
else
  echo "  AVISO: $POOL_DIR não encontrado"
fi

section "6. .user.ini por site"
for domain in "${DOMAINS[@]}"; do
  for ini in "$WWW_ROOT/$domain/.user.ini" "$WWW_ROOT/$domain/public/.user.ini"; do
    if [[ -f "$ini" ]]; then
      echo "  $ini:"
      grep -E 'open_basedir|disable' "$ini" 2>/dev/null | sed 's/^/    /' || cat "$ini" | sed 's/^/    /'
    fi
  done
done

section "7. nginx -T (root/server_name para os domínios)"
if command -v nginx &>/dev/null; then
  if nginx -T 2>/dev/null | grep -A30 "server_name.*admin\.arrow\.app\.br" | head -35 | sed 's/^/  /'; then
    :
  else
    echo "  (grep admin falhou — rode como root)"
  fi
  echo ""
  echo "  --- store ---"
  nginx -T 2>/dev/null | grep -A30 "server_name.*store\.arrow\.app\.br" | head -35 | sed 's/^/  /' || true
  echo ""
  echo "  --- arrow.app.br ---"
  nginx -T 2>/dev/null | grep -A30 "server_name.*arrow\.app\.br" | head -35 | sed 's/^/  /' || true
else
  echo "  nginx não encontrado no PATH"
fi

section "8. Resumo — interpretação rápida"
cat <<'EOF'

  403 plain nginx (sem PHP):
    → nginx bloqueou ANTES do PHP. Não é open_basedir (isso daria erro PHP/500).

  Verifique nesta ordem:
    1. root no vhost = /www/wwwroot/DOMAIN/public (não só /www/wwwroot/DOMAIN)
    2. public/index.php existe e é legível (644, owner www:www)
    3. Todas as pastas pai têm execute (755) para o usuário nginx (www)
    4. Running directory no aaPanel = /public para Laravel
    5. autoindex off + root errado = 403 clássico

  Correção sugerida:
    sudo ./fix-permissions.sh
    sudo ./fix-open-basedir.sh
    Conferir aaPanel → Website → Running directory = /public

  Se arrow.app.br funciona mas store/admin não:
    → compare nginx -T root= entre os três sites
    → compare ls -la public/index.php e permissões das pastas

EOF

echo "==> diagnose-403 concluído."
