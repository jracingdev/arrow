#!/usr/bin/env bash
# Diagnóstico completo Arrow — nginx, arquivos, PHP, curl local.
# Uso: sudo ./diagnose-all.sh [WWW_ROOT]
#
# Cole no terminal aaPanel (root) ou: bash -c '...' com sudo quando necessário.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${1:-/www/wwwroot}"
PHP_VERSION="${PHP_VERSION:-82}"
PHP_BIN="/www/server/php/${PHP_VERSION}/bin/php"
NGINX_USER="${NGINX_USER:-www}"

VHOST_DIR="/www/server/panel/vhost/nginx"
EXTENSION_BASE="${VHOST_DIR}/extension"

# Todos os domínios Arrow (Laravel + landing)
ALL_SITES=("${LARAVEL_SITES[@]}" "$WWW_LANDING")

section() {
  echo ""
  echo "################################################################################"
  echo "# $1"
  echo "################################################################################"
}

laravel_public_root() {
  local domain="$1"
  if [[ "$domain" == "$WWW_LANDING" ]]; then
    echo "$WWW_ROOT/$domain"
  else
    echo "$WWW_ROOT/$domain/public"
  fi
}

echo "==> diagnose-all — Arrow aaPanel"
echo "    Data: $(date -Iseconds 2>/dev/null || date)"
echo "    WWW_ROOT: $WWW_ROOT"
echo "    PHP: $PHP_BIN"
echo "    Domínios: ${ALL_SITES[*]}"

# -----------------------------------------------------------------------------
section "1. Arquivos por site (index.php, vendor, ls -la)"
# -----------------------------------------------------------------------------
for domain in "${ALL_SITES[@]}"; do
  SITE="$WWW_ROOT/$domain"
  PUBLIC="$(laravel_public_root "$domain")"
  INDEX="$PUBLIC/index.php"

  echo ""
  echo "---- $domain ----"
  echo "    Site root:  $SITE"
  echo "    Doc root:   $PUBLIC"

  if [[ ! -d "$SITE" ]]; then
    echo "    ERRO: pasta do site não existe!"
    continue
  fi

  echo "    Site root (ls -lad):"
  ls -lad "$SITE" 2>/dev/null | sed 's/^/      /' || echo "      (ls falhou)"

  if [[ -d "$PUBLIC" ]]; then
    echo "    Doc root (ls -lad):"
    ls -lad "$PUBLIC" 2>/dev/null | sed 's/^/      /' || true
  else
    echo "    ERRO: doc root não existe: $PUBLIC"
  fi

  if [[ -f "$INDEX" ]]; then
    echo "    OK: $INDEX existe"
    ls -la "$INDEX" 2>/dev/null | sed 's/^/      /' || true
  else
    echo "    ERRO: $INDEX NÃO EXISTE"
  fi

  if [[ "$domain" != "$WWW_LANDING" ]]; then
    VENDOR="$SITE/vendor/autoload.php"
    if [[ -f "$VENDOR" ]]; then
      echo "    OK: vendor/autoload.php existe"
      ls -la "$VENDOR" 2>/dev/null | sed 's/^/      /' || true
    else
      echo "    ERRO: $VENDOR ausente — rode post-deploy.sh / composer install"
    fi
  fi

  echo "    Conteúdo doc root (primeiros 12 itens):"
  ls -la "$PUBLIC" 2>/dev/null | head -12 | sed 's/^/      /' || true
done

# -----------------------------------------------------------------------------
section "2. Vhosts principais (cat arrow, admin, store)"
# -----------------------------------------------------------------------------
for domain in "${LARAVEL_SITES[@]}"; do
  CONF="${VHOST_DIR}/${domain}.conf"
  echo ""
  echo "---- $CONF ----"
  if [[ -f "$CONF" ]]; then
    grep -nE '^\s*(server_name|root|index|listen|include|location)' "$CONF" 2>/dev/null \
      | sed 's/^/    /' || cat "$CONF" | sed 's/^/    /'
  else
    echo "    ERRO: arquivo não existe!"
    ls -la "${VHOST_DIR}/"*"${domain}"* 2>/dev/null | sed 's/^/    /' || true
  fi
done

# -----------------------------------------------------------------------------
section "3. Extension configs (/www/server/panel/vhost/nginx/extension/)"
# -----------------------------------------------------------------------------
for domain in "${LARAVEL_SITES[@]}"; do
  EXT_DIR="${EXTENSION_BASE}/${domain}"
  echo ""
  echo "---- extension/$domain ----"
  if [[ -d "$EXT_DIR" ]]; then
    shopt -s nullglob
    ext_files=("$EXT_DIR"/*.conf)
    shopt -u nullglob
    if [[ ${#ext_files[@]} -eq 0 ]]; then
      echo "    (pasta vazia — sem .conf)"
    else
      for ext in "${ext_files[@]}"; do
        echo "    --- $(basename "$ext") ---"
        grep -nE 'root|server_name|open_basedir|fastcgi_param' "$ext" 2>/dev/null \
          | sed 's/^/      /' || echo "      (sem root/open_basedir)"
      done
    fi
  else
    echo "    (pasta extension não existe)"
  fi
done

# grep global em extension
echo ""
echo "---- grep root em extension/ (todos) ----"
grep -rnE 'root[[:space:]]+/www/wwwroot/(arrow|admin|store)\.arrow' "$EXTENSION_BASE" 2>/dev/null \
  | sed 's/^/    /' || echo "    (nenhuma linha)"

# -----------------------------------------------------------------------------
section "4. nginx -T — bloco server completo por domínio"
# -----------------------------------------------------------------------------
if ! command -v nginx &>/dev/null; then
  echo "    nginx não encontrado no PATH"
else
  for domain in "${ALL_SITES[@]}"; do
    echo ""
    echo "---- server block: $domain ----"
    # Extrai bloco server que contém server_name com o domínio
    nginx -T 2>/dev/null | awk -v dom="$domain" '
      BEGIN { in_server=0; depth=0; buf="" }
      /server[[:space:]]*\{/ {
        if (in_server) { buf="" }
        in_server=1; depth=1; buf=$0 "\n"; next
      }
      in_server {
        buf = buf $0 "\n"
        if (match($0, /\{/)) depth += gsub(/\{/, "&")
        if (match($0, /\}/)) depth -= gsub(/\}/, "&")
        if (depth <= 0) {
          if (buf ~ ("server_name[^;]*" dom)) print buf
          in_server=0; buf=""
        }
      }
    ' | sed 's/^/    /' || true

    if ! nginx -T 2>/dev/null | grep -q "server_name.*${domain}"; then
      echo "    AVISO: server_name $domain NÃO encontrado em nginx -T"
    fi
  done
fi

# -----------------------------------------------------------------------------
section "5. nginx -T — linhas root (grep rápido)"
# -----------------------------------------------------------------------------
echo ""
echo "---- grep root /www/wwwroot/(arrow|admin|store).arrow ----"
nginx -T 2>/dev/null | grep -E 'root.*/www/wwwroot/(arrow|admin|store)\.arrow' \
  | sed 's/^[[:space:]]*/    /' || echo "    (nenhuma linha — rode como root)"

for domain in "${LARAVEL_SITES[@]}"; do
  expected="$WWW_ROOT/$domain/public"
  if nginx -T 2>/dev/null | grep -qE "root[[:space:]]+${expected}[[:space:]]*;"; then
    echo "    OK: $domain → root ${expected}"
  else
    echo "    FALHA: $domain NÃO tem root ${expected} em nginx -T"
    nginx -T 2>/dev/null | grep -E "root.*/www/wwwroot/${domain}" | sed 's/^/      atual: /' || true
  fi
done

# -----------------------------------------------------------------------------
section "6. .user.ini — open_basedir"
# -----------------------------------------------------------------------------
for domain in "${LARAVEL_SITES[@]}"; do
  echo ""
  echo "---- $domain ----"
  for ini in "$WWW_ROOT/$domain/.user.ini" "$WWW_ROOT/$domain/public/.user.ini"; do
    if [[ -f "$ini" ]]; then
      echo "    $ini"
      lsattr "$ini" 2>/dev/null | sed 's/^/      attrs: /' || true
      grep -E 'open_basedir|disable' "$ini" 2>/dev/null | sed 's/^/      /' \
        || cat "$ini" | sed 's/^/      /'
    else
      echo "    (ausente) $ini"
    fi
  done
done

# -----------------------------------------------------------------------------
section "7. curl -I localhost (Host header por domínio)"
# -----------------------------------------------------------------------------
for domain in "${ALL_SITES[@]}"; do
  echo ""
  echo "---- curl -I -H Host:$domain http://127.0.0.1/ ----"
  curl -sI -H "Host: $domain" "http://127.0.0.1/" 2>/dev/null | head -20 | sed 's/^/    /' \
    || echo "    curl falhou"
done

# -----------------------------------------------------------------------------
section "8. PHP $PHP_VERSION — teste rápido"
# -----------------------------------------------------------------------------
if [[ -x "$PHP_BIN" ]]; then
  echo "    Versão:"
  "$PHP_BIN" -v | head -1 | sed 's/^/      /'
  echo ""
  echo "    php -r echo (OK se imprimir OK):"
  "$PHP_BIN" -r 'echo "      PHP_OK\n";' 2>&1 | sed 's/^/    /' || echo "    ERRO no php -r"
  echo ""
  echo "    Extensões críticas:"
  for ext in fileinfo mbstring pdo_mysql openssl; do
    if "$PHP_BIN" -m 2>/dev/null | grep -qi "^${ext}$"; then
      echo "      OK: $ext"
    else
      echo "      FALTA: $ext"
    fi
  done
else
  echo "    ERRO: $PHP_BIN não encontrado"
fi

# -----------------------------------------------------------------------------
section "9. Loop vhost *.conf (root em cada arquivo)"
# -----------------------------------------------------------------------------
echo ""
echo "---- for f in ${VHOST_DIR}/*.conf; do grep root ----"
if [[ -d "$VHOST_DIR" ]]; then
  for f in "$VHOST_DIR"/*.conf; do
    [[ -f "$f" ]] || continue
    if grep -qE 'arrow\.app\.br|store\.arrow|admin\.arrow' "$f" 2>/dev/null; then
      echo "    --- $(basename "$f") ---"
      grep -E '^\s*root\s' "$f" 2>/dev/null | sed 's/^/      /' || echo "      (sem root)"
    fi
  done
else
  echo "    ERRO: $VHOST_DIR não existe"
fi

# -----------------------------------------------------------------------------
section "10. Resumo / próximos passos"
# -----------------------------------------------------------------------------
cat <<'EOF'

  arrow.app.br AUSENTE no grep nginx -T?
    → root ainda sem /public OU vhost em outro arquivo (extension, nome diferente)
    → confira: cat /www/server/panel/vhost/nginx/arrow.app.br.conf
    → extension pode sobrescrever root de volta para raiz do site

  admin/store com /public mas arrow sem?
    → arrow.app.br.conf não foi patchado (sed com glob falhou sem bash -c)
    → aaPanel "Running directory" pode ter sido alterado só em admin/store

  Correção recomendada:
    cd /www/wwwroot/arrow-repo/deploy
    sudo git fetch origin main && sudo git reset --hard origin/main
    sudo chmod +x *.sh && sudo ./fix-arrow-complete.sh

  Diagnóstico salvo?  sudo ./diagnose-all.sh | tee /tmp/diagnose-all.log

EOF

echo "==> diagnose-all concluído."
