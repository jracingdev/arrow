#!/usr/bin/env bash
# Sincroniza o monorepo Arrow para as pastas de produção no aaPanel.
# Uso: ./deploy.sh [REPO_ROOT] [WWW_ROOT]
set -euo pipefail

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

rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/website/" "$WWW_ROOT/arrow_app_br/"
rsync -av "$REPO_ROOT/web/landing/" "$WWW_ROOT/lp_arrow_app_br/"
rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/store/" "$WWW_ROOT/store_arrow_app_br/"
rsync -av "${RSYNC_EXCLUDES[@]}" "$REPO_ROOT/web/admin/" "$WWW_ROOT/admin_arrow_app_br/"

echo "==> Deploy de arquivos concluído."
echo "    Execute composer install e php artisan config:cache em cada painel Laravel."
