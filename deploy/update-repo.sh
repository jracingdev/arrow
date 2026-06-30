#!/usr/bin/env bash
# Atualiza arrow-repo no servidor descartando alterações locais.
# Uso: sudo ./update-repo.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

cd "$REPO_ROOT"
echo "==> Atualizando $REPO_ROOT"
git fetch origin main
git reset --hard origin/main
chmod +x "$SCRIPT_DIR"/*.sh
echo "==> Repo sincronizado com origin/main"
git log -1 --oneline
