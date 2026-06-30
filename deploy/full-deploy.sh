#!/usr/bin/env bash
# Deploy completo no servidor aaPanel (clone/pull + sync + pós-deploy).
# Uso: ./full-deploy.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=sites.conf
source "$SCRIPT_DIR/sites.conf"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"
REPO_CLONE="$WWW_ROOT/arrow-repo"
REPO_URL="${REPO_URL:-https://github.com/jracingdev/arrow.git}"

echo "==> Arrow — deploy completo"
echo "    Repo: $REPO_CLONE"
echo "    Sites: $WWW_ROOT"
echo ""

if [[ -d "$REPO_CLONE/.git" ]]; then
  echo "==> Atualizando repositório..."
  git config --global --add safe.directory "$REPO_CLONE" 2>/dev/null || true
  cd "$REPO_CLONE"
  git pull origin main
else
  echo "==> Clonando repositório..."
  git clone "$REPO_URL" "$REPO_CLONE"
  cd "$REPO_CLONE"
fi

echo ""
echo "==> Backup rápido dos .env (se existirem)..."
BACKUP_DIR="$WWW_ROOT/arrow-backups/$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
for site in "${LARAVEL_SITES[@]}"; do
  if [[ -f "$WWW_ROOT/$site/.env" ]]; then
    cp "$WWW_ROOT/$site/.env" "$BACKUP_DIR/${site}.env"
    echo "    Backup: $site/.env"
  fi
done

echo ""
bash "$SCRIPT_DIR/deploy.sh" "$REPO_CLONE" "$WWW_ROOT"

echo ""
bash "$SCRIPT_DIR/post-deploy.sh" "$WWW_ROOT"

echo ""
echo "==> Deploy completo finalizado."
echo ""
echo "Checklist manual:"
echo "  1. Confirme DB_PASSWORD em cada .env (aaPanel → Banco de Dados)"
echo "  2. Confirme credenciais Firebase nos .env Laravel"
echo "  3. APP_ENV=production e APP_DEBUG=false"
echo "  4. Document root: Laravel = /public | Landing = /"
echo "  5. Teste: arrow.app.br | lp.arrow.app.br | store.arrow.app.br | admin.arrow.app.br"
