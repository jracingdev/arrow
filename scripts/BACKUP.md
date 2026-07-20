# Backup MySQL — Arrow

Script: [`deploy/backup-mysql.sh`](../deploy/backup-mysql.sh)

Faz dump gzip dos 3 bancos de produção lendo `DB_*` de cada `.env` (sem imprimir senhas).

| Painel | Pasta aaPanel | Banco |
|--------|---------------|-------|
| Website | `/www/wwwroot/arrow.app.br` | `arrow_website_db` |
| Store | `/www/wwwroot/store.arrow.app.br` | `arrow_store_db` |
| Admin | `/www/wwwroot/admin.arrow.app.br` | `arrow_admin_db` |

## Uso no servidor

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo chmod +x backup-mysql.sh
sudo ./backup-mysql.sh
```

Destino padrão: `/www/backup/arrow-mysql/AAAAMMDD-HHMMSS/*.sql.gz`

Destino customizado:

```bash
sudo ./backup-mysql.sh /caminho/para/backups
```

Retenção (padrão 14 dias):

```bash
BACKUP_KEEP_DAYS=7 sudo ./backup-mysql.sh
```

## Cron diário (recomendado)

Como root no aaPanel / crontab:

```bash
# Backup MySQL Arrow — 03:15 diário
15 3 * * * /www/wwwroot/arrow-repo/deploy/backup-mysql.sh >> /var/log/arrow-mysql-backup.log 2>&1
```

## Restaurar um dump

```bash
# Exemplo: website
gunzip -c /www/backup/arrow-mysql/20260720-031500/arrow_website_db.sql.gz \
  | mysql -u arrow_website_adm -p arrow_website_db
```

> Nunca versionar dumps no Git. Manter `chmod 700` na pasta de backup.
