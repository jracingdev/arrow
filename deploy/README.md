# Deploy Arrow — aaPanel / Nginx

Guia de implantação no servidor de produção **56.125.221.106** (aaPanel, PHP 8.2, Nginx 1.24, MySQL 8.0.45, SSL).

## Início rápido (terminal do aaPanel)

No servidor, use o **Terminal do aaPanel como root** (não como `ubuntu` SSH, se der permissão negada):

```bash
# Diagnóstico
whoami
ls -la /www/wwwroot | head
which git
git --version

# Clone (sem esconder erros)
cd /www/wwwroot
git clone https://github.com/jracingdev/arrow.git arrow-repo

# Se "Permission denied":
# sudo git clone https://github.com/jracingdev/arrow.git arrow-repo
# sudo chown -R www:www arrow-repo

cd /www/wwwroot/arrow-repo/deploy
chmod +x *.sh
./full-deploy.sh
```

Depois configure as senhas MySQL nos `.env` (se ainda não existirem no servidor) e rode:

```bash
./check-env.sh
```

Para atualizações futuras, basta `./full-deploy.sh` novamente (faz `git pull` + sync + cache).

### Scripts disponíveis

| Script | Função |
|--------|--------|
| `full-deploy.sh` | Clone/pull + sync + composer + cache (tudo) |
| `deploy.sh` | Só copia arquivos para as pastas dos sites |
| `post-deploy.sh` | Composer, permissões e cache Laravel |
| `check-env.sh` | Valida `.env` sem expor senhas |
| `fix-php-aapanel.sh` | Habilita fileinfo no PHP 8.2 do aaPanel |
| `set-production.sh` | APP_ENV=production e APP_DEBUG=false |

## Mapeamento domínio → pasta no servidor

| Domínio | Pasta aaPanel | Origem no monorepo | Document root |
|---------|---------------|-------------------|---------------|
| https://arrow.app.br | `/www/wwwroot/arrow.app.br` | `web/website` | `public/` |
| https://lp.arrow.app.br | `/www/wwwroot/lp.arrow.app.br` | `web/landing` | raiz (`/`) |
| https://store.arrow.app.br | `/www/wwwroot/store.arrow.app.br` | `web/store` | `public/` |
| https://admin.arrow.app.br | `/www/wwwroot/admin.arrow.app.br` | `web/admin` | `public/` |

> Os caminhos exatos podem variar conforme a instalação do aaPanel. Ajuste se o seu painel usar outro prefixo (ex.: `/www/server/panel/vhost`).

## Bancos MySQL em produção

| Painel | Banco | Usuário | Host |
|--------|-------|---------|------|
| Website | `arrow_website_db` | `arrow_website_adm` | `localhost` |
| Store | `arrow_store_db` | `arrow_store_adm` | `localhost` |
| Admin | `arrow_admin_db` | `arrow_admin_adm` | `localhost` |
| Landing | — | — | sem banco |

As senhas são definidas no aaPanel (MySQL → usuários). **Nunca** as coloque no Git.

## 1. Clonar ou atualizar o repositório

```bash
# Primeira vez
cd /www/wwwroot
git clone https://github.com/jracingdev/arrow.git arrow-repo

# Atualizações
cd /www/wwwroot/arrow-repo
git pull origin main
```

## 2. Sincronizar arquivos para as pastas dos sites

### Opção A — script (recomendado)

No servidor Linux:

```bash
cd /www/wwwroot/arrow-repo/deploy
chmod +x deploy.sh
./deploy.sh
```

No Windows (desenvolvimento / sync via rede):

```powershell
.\deploy\deploy.ps1 -ServerRoot "D:\servidor\wwwroot"
```

### Opção B — manual com rsync

```bash
REPO=/www/wwwroot/arrow-repo
WWW=/www/wwwroot

rsync -av --delete \
  --exclude='.env' --exclude='vendor/' --exclude='node_modules/' \
  --exclude='storage/logs/*' --exclude='bootstrap/cache/*' \
  $REPO/web/website/ $WWW/arrow.app.br/

rsync -av --delete $REPO/web/landing/ $WWW/lp.arrow.app.br/

rsync -av --delete \
  --exclude='.env' --exclude='vendor/' --exclude='node_modules/' \
  --exclude='storage/logs/*' --exclude='bootstrap/cache/*' \
  $REPO/web/store/ $WWW/store.arrow.app.br/

rsync -av --delete \
  --exclude='.env' --exclude='vendor/' --exclude='node_modules/' \
  --exclude='storage/logs/*' --exclude='bootstrap/cache/*' \
  $REPO/web/admin/ $WWW/admin.arrow.app.br/
```

> **Importante:** não use `--delete` na primeira migração se houver uploads em `storage/app/public` — faça backup antes.

## 3. Composer (cada painel Laravel)

```bash
cd /www/wwwroot/arrow.app.br && composer install --no-dev --optimize-autoloader
cd /www/wwwroot/store.arrow.app.br && composer install --no-dev --optimize-autoloader
cd /www/wwwroot/admin.arrow.app.br && composer install --no-dev --optimize-autoloader
```

## 4. Configurar `.env` por painel

Copie de `.env.example` apenas se o `.env` ainda não existir no servidor:

```bash
cp .env.example .env   # só na primeira vez
php artisan key:generate   # só se APP_KEY estiver vazio
```

### Website — `arrow.app.br/.env`

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://arrow.app.br

DB_HOST=127.0.0.1
DB_DATABASE=arrow_website_db
DB_USERNAME=arrow_website_adm
DB_PASSWORD=<senha do aaPanel>

FIREBASE_APIKEY=<...>
FIREBASE_AUTH_DOMAIN=<...>
FIREBASE_DATABASE_URL=<...>
FIREBASE_PROJECT_ID=<...>
FIREBASE_STORAGE_BUCKET=<...>
FIREBASE_MESSAAGING_SENDER_ID=<...>
FIREBASE_APP_ID=<...>
FIREBASE_MEASUREMENT_ID=<...>
```

### Store — `store.arrow.app.br/.env`

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://store.arrow.app.br

DB_HOST=127.0.0.1
DB_DATABASE=arrow_store_db
DB_USERNAME=arrow_store_adm
DB_PASSWORD=<senha do aaPanel>
```

### Admin — `admin.arrow.app.br/.env`

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin.arrow.app.br

DB_HOST=127.0.0.1
DB_DATABASE=arrow_admin_db
DB_USERNAME=arrow_admin_adm
DB_PASSWORD=<senha do aaPanel>
```

### Landing — `lp.arrow.app.br`

Sem `.env` e sem banco de dados.

## 5. Permissões e cache Laravel

```bash
for site in arrow.app.br store.arrow.app.br admin.arrow.app.br; do
  cd /www/wwwroot/$site
  chown -R www:www storage bootstrap/cache
  chmod -R 775 storage bootstrap/cache
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
done
```

## 6. Nginx — document root

No aaPanel, confirme o **Running directory** de cada site:

| Site | Running directory |
|------|-------------------|
| arrow.app.br | `/public` |
| lp.arrow.app.br | `/` (raiz) |
| store.arrow.app.br | `/public` |
| admin.arrow.app.br | `/public` |

Exemplos de blocos `location` estão em `deploy/nginx-snippets/`.

### Regra Laravel (website, store, admin)

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/tmp/php-cgi-82.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

Ajuste o socket PHP conforme sua versão no aaPanel (`php-cgi-82.sock` para PHP 8.2).

## 7. SSL

O SSL já está ativo no servidor. No aaPanel: **Website → SSL → Let's Encrypt** para renovação automática.

## 8. Firebase Functions (opcional)

```bash
cd /www/wwwroot/arrow-repo/firebase/functions
npm install
firebase deploy --only functions
```

## Checklist pós-deploy

- [ ] https://arrow.app.br carrega o painel website
- [ ] https://lp.arrow.app.br carrega a landing
- [ ] https://store.arrow.app.br carrega o painel lojista
- [ ] https://admin.arrow.app.br carrega o painel admin
- [ ] Login funciona em cada painel Laravel
- [ ] `APP_DEBUG=false` em todos os `.env` de produção
- [ ] Uploads em `storage/app/public` acessíveis (symlink `public/storage` se necessário)

## Solução de problemas

| Sintoma | Causa provável | Correção |
|---------|----------------|----------|
| **404** no Laravel | Document root errado ou `vendor/` ausente | Running directory = `/public` + `./fix-php-aapanel.sh` + `./post-deploy.sh` |
| **403** no Laravel | Permissões ou `vendor/` ausente | `chown -R www:www` + `./post-deploy.sh` |
| **composer: ext-fileinfo** | Extensão PHP desabilitada | `sudo ./fix-php-aapanel.sh` |
| **composer: php >=8.2** | CLI usa PHP 8.1 | Scripts usam `/www/server/php/82/bin/php` automaticamente |
| **500** | `.env` ou banco incorreto | `./check-env.sh` + conferir senha MySQL no aaPanel |
| **store** não resolve DNS | Subdomínio não criado no DNS | Adicionar registro A `store` → `56.125.221.106` |
| Landing OK, Laravel não | Deploy antigo ainda ativo | Rodar `./full-deploy.sh` para sincronizar monorepo |

## Rollback

Mantenha backup das pastas e `.env` antes de cada deploy:

```bash
tar -czf backup-$(date +%Y%m%d).tar.gz arrow.app.br store.arrow.app.br admin.arrow.app.br lp.arrow.app.br
```
