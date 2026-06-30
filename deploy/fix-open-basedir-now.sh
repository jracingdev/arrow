#!/usr/bin/env bash
# Correção imediata open_basedir — vhosts aaPanel + .user.ini imutáveis.
# Rode após git reset --hard quando admin/store ainda mostram erro open_basedir.
#
# Uso no servidor:
#   cd /www/wwwroot/arrow-repo/deploy
#   sudo chmod +x *.sh
#   sudo ./fix-open-basedir-now.sh
#
# Equivale a: fix-open-basedir.sh && fix-user-ini.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "========================================"
echo " Arrow — fix-open-basedir-now"
echo "========================================"
echo ""

bash "$SCRIPT_DIR/fix-open-basedir.sh"
echo ""
bash "$SCRIPT_DIR/fix-user-ini.sh"

echo ""
echo "========================================"
echo " Concluído — teste admin e store"
echo "========================================"
