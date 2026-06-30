#!/usr/bin/env bash
# fix-all.sh — recuperação completa do servidor aaPanel Arrow
#
# Executa em ordem:
#   1. update-repo.sh      (git fetch + reset --hard origin/main)
#   2. fix-php-aapanel.sh  (fileinfo no PHP 8.2)
#   3. deploy.sh           (rsync monorepo → pastas dos sites)
#   4. post-deploy.sh      (composer + cache Laravel)
#   5. fix-permissions.sh  (chown/chmod — corrige 403 nginx)
#   6. set-production.sh   (APP_ENV=production)
#   7. fix-open-basedir.sh (corrige open_basedir nos vhosts)
#   8. check-env.sh        (valida .env)
#
# Uso no servidor (como root):
#   cd /www/wwwroot/arrow-repo/deploy
#   sudo chmod +x *.sh
#   sudo ./fix-all.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
WWW_ROOT="${WWW_ROOT:-/www/wwwroot}"

# Força PHP 8.2 para composer/artisan (CLI padrão do aaPanel costuma ser 8.1)
if [[ -x /www/server/php/82/bin/php ]]; then
  export PHP_BIN="/www/server/php/82/bin/php"
else
  export PHP_BIN="${PHP_BIN:-php}"
fi

echo "========================================"
echo " Arrow — fix-all (recuperação completa)"
echo "========================================"
echo " Repo:  $REPO_ROOT"
echo " Sites: $WWW_ROOT"
echo " PHP:   $PHP_BIN"
echo ""

run_step() {
  local n="$1"
  local name="$2"
  shift 2
  echo ""
  echo ">>> [$n/8] $name"
  echo "----------------------------------------"
  "$@"
}

run_step 1 "Atualizando repositório (git reset --hard)" \
  bash "$SCRIPT_DIR/update-repo.sh"

run_step 2 "Corrigindo PHP 8.2 (fileinfo)" \
  bash "$SCRIPT_DIR/fix-php-aapanel.sh"

run_step 3 "Sincronizando arquivos (deploy.sh)" \
  bash "$SCRIPT_DIR/deploy.sh" "$REPO_ROOT" "$WWW_ROOT"

run_step 4 "Pós-deploy (composer + cache)" \
  bash "$SCRIPT_DIR/post-deploy.sh" "$WWW_ROOT"

run_step 5 "Corrigindo permissões (403 nginx)" \
  bash "$SCRIPT_DIR/fix-permissions.sh" "$WWW_ROOT"

run_step 6 "Modo produção (.env)" \
  bash "$SCRIPT_DIR/set-production.sh" "$WWW_ROOT"

run_step 7 "Corrigindo open_basedir (nginx/PHP vhosts)" \
  bash "$SCRIPT_DIR/fix-open-basedir.sh"

run_step 8 "Verificando .env" \
  bash "$SCRIPT_DIR/check-env.sh" "$WWW_ROOT"

echo ""
echo "========================================"
echo " fix-all concluído"
echo "========================================"
echo ""
echo "Teste os sites:"
echo "  https://arrow.app.br"
echo "  https://store.arrow.app.br"
echo "  https://admin.arrow.app.br"
echo "  https://lp.arrow.app.br"
echo ""
echo "Se algum Laravel ainda falhar com open_basedir, corrija manualmente no aaPanel"
echo "(veja deploy/README.md → seção open_basedir)."
