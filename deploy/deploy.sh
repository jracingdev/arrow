#!/usr/bin/env bash
# Sincroniza o monorepo Arrow para as pastas de produção no aaPanel.
# Uso: ./deploy.sh [REPO_ROOT] [WWW_ROOT]
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"

REPO_ROOT="${1:-/www/wwwroot/arrow-repo}"
WWW_ROOT="${2:-/www/wwwroot}"

RSYNC_EXCLUDES=(
  --exclude='.env'
  --exclude='.git'
  --exclude='vendor/'
  --exclude='node_modules/'
  --exclude='storage/logs/*'
  --exclude='bootstrap/cache/*'
)

echo "==> Sincronizando de $REPO_ROOT para $WWW_ROOT"
echo "    Website -> $WWW_WEBSITE"
echo "    Landing -> $WWW_LANDING"
echo "    Store   -> $WWW_STORE"
echo "    Admin   -> $WWW_ADMIN"
echo ""

rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/website/" "$WWW_ROOT/$WWW_WEBSITE/"
rsync -av "$REPO_ROOT/web/landing/" "$WWW_ROOT/$WWW_LANDING/"
rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/store/" "$WWW_ROOT/$WWW_STORE/"
rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/admin/" "$WWW_ROOT/$WWW_ADMIN/"

echo ""
echo "==> Deploy de arquivos concluído."
echo "    Execute: ./post-deploy.sh"
