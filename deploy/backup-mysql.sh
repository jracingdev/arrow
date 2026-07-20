#!/usr/bin/env bash
# Backup MySQL dos 3 bancos Arrow (website, store, admin).
# Lê DB_* de cada .env — NÃO imprime senhas.
#
# Uso:
#   ./backup-mysql.sh              # destino padrão /www/backup/arrow-mysql
#   ./backup-mysql.sh /caminho     # destino customizado
#   WWW_ROOT=/www/wwwroot ./backup-mysql.sh
#
# Retenção: BACKUP_KEEP_DAYS=14 (padrão) — remove dumps mais antigos.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"
DEST_ROOT="${1:-/www/backup/arrow-mysql}"
KEEP_DAYS="${BACKUP_KEEP_DAYS:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"
DEST="$DEST_ROOT/$STAMP"

env_val() {
  local file="$1"
  local key="$2"
  grep "^${key}=" "$file" 2>/dev/null | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'" | tr -d '\r'
}

MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"
if [[ ! -x "$(command -v "$MYSQLDUMP_BIN" 2>/dev/null || true)" ]]; then
  if [[ -x /www/server/mysql/bin/mysqldump ]]; then
    MYSQLDUMP_BIN=/www/server/mysql/bin/mysqldump
  fi
fi

mkdir -p "$DEST"
chmod 700 "$DEST_ROOT" 2>/dev/null || true
chmod 700 "$DEST"

echo "==> Backup MySQL Arrow → $DEST"
echo ""

OK=0
FAIL=0

for site in "${LARAVEL_SITES[@]}"; do
  ENV_FILE="$WWW_ROOT/$site/.env"
  if [[ ! -f "$ENV_FILE" ]]; then
    echo "  [PULOU] $site — .env ausente"
    FAIL=$((FAIL + 1))
    continue
  fi

  host=$(env_val "$ENV_FILE" DB_HOST)
  port=$(env_val "$ENV_FILE" DB_PORT)
  name=$(env_val "$ENV_FILE" DB_DATABASE)
  user=$(env_val "$ENV_FILE" DB_USERNAME)
  pass=$(env_val "$ENV_FILE" DB_PASSWORD)

  host="${host:-127.0.0.1}"
  port="${port:-3306}"

  if [[ -z "$name" || -z "$user" ]]; then
    echo "  [FALHA] $site — DB_DATABASE/DB_USERNAME vazios"
    FAIL=$((FAIL + 1))
    continue
  fi

  out="$DEST/${name}.sql.gz"
  echo "  Dumping $name ($site)..."

  # MYSQL_PWD evita senha na linha de comando (visível em ps)
  export MYSQL_PWD="$pass"
  if "$MYSQLDUMP_BIN" \
    -h "$host" -P "$port" -u "$user" \
    --single-transaction --routines --triggers --events \
    --default-character-set=utf8mb4 \
    "$name" | gzip -c > "$out"; then
    unset MYSQL_PWD
    size=$(du -h "$out" | awk '{print $1}')
    chmod 600 "$out"
    echo "  [OK] $out ($size)"
    OK=$((OK + 1))
  else
    unset MYSQL_PWD
    rm -f "$out"
    echo "  [FALHA] dump de $name"
    FAIL=$((FAIL + 1))
  fi
done

echo ""
if [[ "$KEEP_DAYS" =~ ^[0-9]+$ ]] && [[ "$KEEP_DAYS" -gt 0 ]]; then
  echo "==> Removendo backups com mais de ${KEEP_DAYS} dias em $DEST_ROOT"
  find "$DEST_ROOT" -mindepth 1 -maxdepth 1 -type d -mtime "+${KEEP_DAYS}" -exec rm -rf {} + 2>/dev/null || true
fi

# Manifesto sem secrets
{
  echo "stamp=$STAMP"
  echo "host=$(hostname 2>/dev/null || echo unknown)"
  echo "ok=$OK"
  echo "fail=$FAIL"
} > "$DEST/MANIFEST.txt"
chmod 600 "$DEST/MANIFEST.txt"

echo ""
echo "==> Concluído: $OK ok, $FAIL falha(s)."
echo "    Pasta: $DEST"
[[ "$FAIL" -eq 0 ]] || exit 1
