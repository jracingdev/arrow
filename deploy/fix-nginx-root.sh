#!/usr/bin/env bash
# Corrige document root nos vhosts aaPanel para Laravel.
# Laravel exige root apontando para public/ — não a raiz do site.
#
# Uso: sudo ./fix-nginx-root.sh
#
# Sintoma típico: HTTP 403/404 — nginx não encontra index.php na raiz do site.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"

VHOST_DIR="/www/server/panel/vhost/nginx"
EXTENSION_BASE="${VHOST_DIR}/extension"
ALT_VHOST_DIR="/www/server/nginx/conf/vhost"

# Ordem explícita — arrow.app.br primeiro (costuma ser o que falha no grep)
DOMAINS=("$WWW_WEBSITE" "$WWW_STORE" "$WWW_ADMIN")

# Vhosts principais (patch explícito, não só glob)
EXPLICIT_VHOSTS=(
  "${VHOST_DIR}/arrow.app.br.conf"
  "${VHOST_DIR}/admin.arrow.app.br.conf"
  "${VHOST_DIR}/store.arrow.app.br.conf"
)

PATCHED=0
FIXED_DOUBLE=0

print_manual_fix() {
  cat <<'EOF'

================================================================================
CORREÇÃO MANUAL — document root nginx (Laravel)
================================================================================

Para cada site Laravel (arrow.app.br, store.arrow.app.br, admin.arrow.app.br):

  1. aaPanel → Website → clique no domínio
  2. Site directory → Running directory = /public
  3. Anti-XSS attack = OFF (Laravel)
  4. Confirme no vhost:
       root /www/wwwroot/DOMAIN/public;

Comandos sed (use bash -c se glob falhar):

  sudo bash -c "sed -i 's|root /www/wwwroot/arrow.app.br;|root /www/wwwroot/arrow.app.br/public;|' /www/server/panel/vhost/nginx/arrow.app.br.conf"
  sudo bash -c "sed -i 's|root /www/wwwroot/store.arrow.app.br;|root /www/wwwroot/store.arrow.app.br/public;|' /www/server/panel/vhost/nginx/store.arrow.app.br.conf"
  sudo bash -c "sed -i 's|root /www/wwwroot/admin.arrow.app.br;|root /www/wwwroot/admin.arrow.app.br/public;|' /www/server/panel/vhost/nginx/admin.arrow.app.br.conf"
  sudo nginx -t && sudo nginx -s reload

Verifique arrow.app.br especificamente:
  sudo nginx -T 2>/dev/null | grep 'root.*/www/wwwroot/arrow.app.br'

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
    shopt -s nullglob
    local ext
    for ext in "$ext_dir"/*.conf; do
      _out+=("$ext")
    done
    shopt -u nullglob
  fi

  if [[ -d "$ALT_VHOST_DIR" ]]; then
    local alt_conf="${ALT_VHOST_DIR}/${domain}.conf"
    [[ -f "$alt_conf" ]] && _out+=("$alt_conf")
  fi
}

fix_double_public() {
  local file="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"
  local double="${site_root}/public/public"

  if grep -qE "root[[:space:]]+${double}[[:space:]]*;" "$file" 2>/dev/null; then
    cp "$file" "${file}.bak.double.$(date +%Y%m%d%H%M%S)"
    sed -i "s|root ${double};|root ${site_root}/public;|g" "$file"
    echo "  [FIX DOUBLE] $file"
    echo "          root ${double}; → root ${site_root}/public;"
    FIXED_DOUBLE=$((FIXED_DOUBLE + 1))
    PATCHED=$((PATCHED + 1))
    return 0
  fi
  return 1
}

patch_file_for_domain() {
  local file="$1"
  local domain="$2"
  local site_root="$WWW_ROOT/$domain"

  if [[ ! -f "$file" ]]; then
    return 1
  fi

  # Corrige /public/public primeiro
  fix_double_public "$file" "$domain" || true

  if grep -qE "root[[:space:]]+${site_root}/public[[:space:]]*;" "$file" 2>/dev/null; then
    return 1
  fi

  # root sem /public
  if grep -qE "root[[:space:]]+${site_root}[[:space:]]*;" "$file" 2>/dev/null; then
    cp "$file" "${file}.bak.$(date +%Y%m%d%H%M%S)"
    sed -i "s|root ${site_root};|root ${site_root}/public;|g" "$file"
    echo "  [PATCH] $file"
    echo "          root ${site_root}; → root ${site_root}/public;"
    PATCHED=$((PATCHED + 1))
    return 0
  fi

  # root com barra final mas sem public
  if grep -qE "root[[:space:]]+${site_root}/[[:space:]]*;" "$file" 2>/dev/null; then
    cp "$file" "${file}.bak.$(date +%Y%m%d%H%M%S)"
    sed -i "s|root ${site_root}/;|root ${site_root}/public;|g" "$file"
    echo "  [PATCH] $file (trailing slash)"
    PATCHED=$((PATCHED + 1))
    return 0
  fi

  return 1
}

echo "==> fix-nginx-root — document root Laravel (/public)"
echo "    Sites: ${DOMAINS[*]}"
echo "    Raiz esperada: $WWW_ROOT/DOMAIN/public"
echo ""

# --- Passo 1: patch explícito nos três vhosts principais ---
section_explicit() {
  echo "==> Passo 1 — vhosts explícitos (arrow, admin, store)"
  for conf in "${EXPLICIT_VHOSTS[@]}"; do
    if [[ ! -f "$conf" ]]; then
      echo "  AVISO: não existe: $conf"
      continue
    fi
    domain=$(basename "$conf" .conf)
    echo "  --- $conf ---"
    for d in "${DOMAINS[@]}"; do
      if [[ "$domain" == "$d" ]]; then
        patch_file_for_domain "$conf" "$d" || {
          if grep -qE "root[[:space:]]+${WWW_ROOT}/${d}/public[[:space:]]*;" "$conf" 2>/dev/null; then
            echo "  [OK] já aponta para /public"
          else
            echo "  [??] root não reconhecido — confira manualmente:"
            grep -E '^\s*root\s' "$conf" 2>/dev/null | sed 's/^/        /' || true
          fi
        }
        break
      fi
    done
  done
  echo ""
}
section_explicit

# --- Passo 2: extension configs por domínio ---
echo "==> Passo 2 — extension configs"
for domain in "${DOMAINS[@]}"; do
  echo "---- extension/$domain ----"
  ext_dir="${EXTENSION_BASE}/${domain}"
  if [[ -d "$ext_dir" ]]; then
    shopt -s nullglob
    for ext in "$ext_dir"/*.conf; do
      patch_file_for_domain "$ext" "$domain" || {
        if grep -qE "root[[:space:]]+${WWW_ROOT}/${domain}/public[[:space:]]*;" "$ext" 2>/dev/null; then
          echo "  [OK] $ext"
        fi
      }
    done
    shopt -u nullglob
  else
    echo "  (sem pasta extension)"
  fi
done
echo ""

# --- Passo 3: loop for f in vhost/nginx/*.conf (bash expande glob) ---
echo "==> Passo 3 — loop ${VHOST_DIR}/*.conf"
if [[ -d "$VHOST_DIR" ]]; then
  for f in "$VHOST_DIR"/*.conf; do
    [[ -f "$f" ]] || continue
    for domain in "${DOMAINS[@]}"; do
      if grep -q "$domain" "$f" 2>/dev/null || grep -q "${WWW_ROOT}/${domain}" "$f" 2>/dev/null; then
        patch_file_for_domain "$f" "$domain" || true
      fi
    done
  done
else
  echo "  AVISO: $VHOST_DIR não existe"
fi
echo ""

# --- Passo 4: busca recursiva (maxdepth 2) por segurança ---
if [[ -d "$VHOST_DIR" ]]; then
  while IFS= read -r -d '' file; do
    for domain in "${DOMAINS[@]}"; do
      patch_file_for_domain "$file" "$domain" || true
    done
  done < <(find "$VHOST_DIR" -maxdepth 2 -type f -name '*.conf' -print0 2>/dev/null)
fi

echo ""
if [[ "$PATCHED" -gt 0 ]]; then
  echo "==> $PATCHED alteração(ões) ($FIXED_DOUBLE double /public/public). Testando nginx ..."
  if nginx -t 2>/dev/null; then
    nginx -s reload 2>/dev/null || /etc/init.d/nginx reload 2>/dev/null || bt reload nginx 2>/dev/null || true
    echo "    Nginx recarregado."
  else
    echo "    ERRO: nginx -t falhou — restaure os arquivos .bak.*"
    exit 1
  fi
else
  echo "==> Nenhum vhost patchado automaticamente."
  print_manual_fix
fi

echo ""
echo "==> Verificação nginx -T (root por domínio):"
ARROW_OK=0
for domain in "${DOMAINS[@]}"; do
  expected="$WWW_ROOT/$domain/public"
  echo "---- $domain (esperado: root $expected;) ----"
  lines=$(nginx -T 2>/dev/null | grep -E "root.*/www/wwwroot/${domain}" | sed 's/^[[:space:]]*/    /' || true)
  if [[ -n "$lines" ]]; then
    echo "$lines"
  else
    echo "    ERRO: nenhuma linha root para $domain em nginx -T"
  fi
  if nginx -T 2>/dev/null | grep -qE "root[[:space:]]+${expected}[[:space:]]*;"; then
    echo "    [OK] root correto em nginx -T"
    [[ "$domain" == "$WWW_WEBSITE" ]] && ARROW_OK=1
  else
    echo "    [FALHA] root $expected NÃO confirmado — verifique vhost + extension"
  fi
done

echo ""
if [[ "$ARROW_OK" -eq 0 ]]; then
  echo ">>> ATENÇÃO: arrow.app.br ainda NÃO aparece com /public em nginx -T <<<"
  echo "    Rode: cat ${VHOST_DIR}/arrow.app.br.conf | grep root"
  echo "    Rode: grep -r root ${EXTENSION_BASE}/arrow.app.br/ 2>/dev/null"
  print_manual_fix
fi

echo ""
echo "==> Concluído."
