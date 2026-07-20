# Hardening pós-incidente — lembretes (servidor Ubuntu / aaPanel)

Checklist operacional. **Não** cola senhas nem JSON de service account neste arquivo nem no chat.

## 1. Rotacionar senhas MySQL (já vazaram em chat)

No aaPanel → **Database** → cada usuário (`arrow_website_adm`, `arrow_store_adm`, `arrow_admin_adm`):

1. Gerar senha forte nova
2. Atualizar `DB_PASSWORD=` nos três `.env`:
   - `/www/wwwroot/arrow.app.br/.env`
   - `/www/wwwroot/store.arrow.app.br/.env`
   - `/www/wwwroot/admin.arrow.app.br/.env`
3. Limpar cache config:

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo ./set-production.sh   # também reforça APP_DEBUG=false + timezone/locale
# ou só cache:
for s in arrow.app.br store.arrow.app.br admin.arrow.app.br; do
  cd /www/wwwroot/$s && /www/server/php/82/bin/php artisan config:cache
done
```

4. Testar login em cada painel

## 2. Rotacionar service account Firebase

1. [Google Cloud Console](https://console.cloud.google.com/iam-admin/serviceaccounts) → projeto **j-arrow**
2. Conta de serviço usada pelo app → **Keys** → adicionar chave JSON nova
3. Substituir arquivo em cada painel:

```bash
# Após copiar o JSON novo para o servidor (scp/sftp), em cada site:
SITE=arrow.app.br   # repetir store.arrow.app.br e admin.arrow.app.br
sudo mkdir -p /www/wwwroot/$SITE/storage/app/firebase
sudo cp /caminho/seguro/credentials-novo.json \
  /www/wwwroot/$SITE/storage/app/firebase/credentials.json
sudo chown www:www /www/wwwroot/$SITE/storage/app/firebase/credentials.json
sudo chmod 600 /www/wwwroot/$SITE/storage/app/firebase/credentials.json
```

4. No Console GCP: **apagar a chave antiga** da service account
5. Confirmar push/FCM (admin → enviar notificação de teste)

## 3. Permissões `chmod 600` no credentials.json

```bash
for s in arrow.app.br store.arrow.app.br admin.arrow.app.br; do
  f=/www/wwwroot/$s/storage/app/firebase/credentials.json
  if [[ -f "$f" ]]; then
    sudo chown www:www "$f"
    sudo chmod 600 "$f"
    ls -la "$f"
  else
    echo "AUSENTE: $f"
  fi
done
```

Validação automática: `./check-env.sh` (alerta se modo ≠ 600/400).

## 4. Reboot Ubuntu pendente

Após atualizações de kernel (`/var/run/reboot-required`):

```bash
# Só quando puder interromper o serviço (janela de manutenção)
test -f /var/run/reboot-required && cat /var/run/reboot-required.pkgs
# Agendar reboot:
sudo reboot
```

Depois do reboot: `sudo nginx -t && systemctl status nginx mysqld` (ou serviços aaPanel).

## 5. Disco

```bash
df -h /
df -h /www
du -sh /www/wwwroot/* /www/backup 2>/dev/null | sort -h
```

Se `/` > 85%: limpar logs antigos do aaPanel, dumps MySQL velhos, `arrow-backups` e `journalctl --vacuum-time=7d`.

## 6. Comando único pós-deploy (produção)

Após `./full-deploy.sh` (ou sync manual):

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo chmod +x *.sh
sudo ./set-production.sh && sudo ./check-env.sh
```

Opcional no mesmo dia: `sudo ./backup-mysql.sh`

Ver também: [BACKUP.md](BACKUP.md) e [deploy/README.md](../deploy/README.md).
