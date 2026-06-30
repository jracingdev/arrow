#!/usr/bin/env bash
# Configura PHP 8.2 do aaPanel para deploy Laravel (fileinfo + composer).
# Uso: sudo ./fix-php-aapanel.sh
set -euo pipefail

PHP82="/www/server/php/82/bin/php"
PHP82_INI="/www/server/php/82/etc/php.ini"
PHP82_CLI_INI="/www/server/php/82/etc/php-cli.ini"

if [[ ! -x "$PHP82" ]]; then
  echo "ERRO: PHP 8.2 não encontrado em $PHP82"
  echo "      Instale PHP 8.2 no aaPanel: App Store → PHP 8.2"
  exit 1
fi

echo "==> PHP aaPanel"
$PHP82 -v
echo ""

enable_fileinfo() {
  local ini="$1"
  [[ -f "$ini" ]] || return 0
  if $PHP82 -c "$ini" -m 2>/dev/null | grep -qi fileinfo; then
    echo "    [OK] fileinfo já ativo em $ini"
    return 0
  fi
  if grep -qE '^;?extension\s*=\s*fileinfo' "$ini"; then
    sed -i 's/^;extension=fileinfo/extension=fileinfo/' "$ini"
    sed -i 's/^;extension\s*=\s*fileinfo/extension=fileinfo/' "$ini"
    echo "    [OK] fileinfo habilitado em $ini"
  else
    echo "extension=fileinfo" >> "$ini"
    echo "    [OK] fileinfo adicionado em $ini"
  fi
}

echo "==> Habilitando ext-fileinfo"
enable_fileinfo "$PHP82_INI"
enable_fileinfo "$PHP82_CLI_INI"

echo ""
echo "==> Reiniciando PHP-FPM 8.2"
/etc/init.d/php-fpm-82 restart 2>/dev/null || systemctl restart php-fpm-82 2>/dev/null || bt restart php-fpm-82 2>/dev/null || true

echo ""
if $PHP82 -m | grep -qi fileinfo; then
  echo "[OK] ext-fileinfo ativo no CLI PHP 8.2"
else
  echo "[FALHA] fileinfo ainda ausente."
  echo "        No aaPanel: App Store → PHP 8.2 → Installed → marque fileinfo → Salvar"
  exit 1
fi

echo ""
echo "==> Pronto. Agora rode:"
echo "    export PHP_BIN=$PHP82"
echo "    cd /www/wwwroot/arrow-repo/deploy && sudo -E ./post-deploy.sh"
