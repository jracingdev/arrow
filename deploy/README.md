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
| `fix-open-basedir.sh` | Corrige open_basedir nos vhosts (Laravel precisa da raiz do site) |
| `fix-nginx-root.sh` | Corrige document root nginx → `/public` (causa #1 de 403 plain nginx) |
| `fix-permissions.sh` | chown/chmod completo — corrige 403 nginx (www não lê public/) |
| `diagnose-403.sh` | Diagnóstico de 403 plain nginx (root, permissões, vhost) |
| `fix-all.sh` | Recuperação completa: repo + PHP + deploy + permissões + open_basedir + nginx root + check |
| `update-repo.sh` | `git fetch` + `reset --hard origin/main` (resolve conflitos de pull) |
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

## open_basedir — ERRO CRÍTICO

O aaPanel define por padrão `open_basedir` apontando para a pasta **`public/`**. Laravel **não funciona** assim: o PHP precisa ler `vendor/`, `storage/` e `bootstrap/` na **raiz do site** (pai de `public/`).

### Configuração correta no aaPanel

Para **arrow.app.br**, **store.arrow.app.br** e **admin.arrow.app.br**:

1. aaPanel → **Website** → clique no domínio
2. Aba **PHP** (ou configurações PHP do site)
3. Campo **open_basedir**:
   - **CORRETO:** `/www/wwwroot/DOMAIN/:/tmp/` (sem `/public` no final)
   - **ERRADO:** `/www/wwwroot/DOMAIN/public/:/tmp/`
4. Alternativa: desabilitar a restrição **open_basedir** nas configurações PHP do site
5. Salvar e reiniciar PHP-FPM 8.2

Exemplos:

| Site | open_basedir correto |
|------|---------------------|
| arrow.app.br | `/www/wwwroot/arrow.app.br/:/tmp/` |
| store.arrow.app.br | `/www/wwwroot/store.arrow.app.br/:/tmp/` |
| admin.arrow.app.br | `/www/wwwroot/admin.arrow.app.br/:/tmp/` |

> O **document root** continua sendo `/public` — só o **open_basedir** deve apontar para a raiz do site.

### Sintomas quando open_basedir está errado

- Tela branca ou HTTP 500
- Log PHP: `require(.../vendor/autoload.php): open_basedir restriction in effect`
- Log PHP: `file_put_contents(.../storage/logs/...): open_basedir restriction in effect`
- Nginx **404** em rotas Laravel (index.php não consegue carregar o autoload)

### Correção automática no servidor

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo ./fix-open-basedir.sh
```

Ou rode a recuperação completa (recomendado após conflitos de git pull):

```bash
cd /www/wwwroot/arrow-repo
sudo git fetch origin main && sudo git reset --hard origin/main
cd deploy && sudo chmod +x *.sh && sudo ./fix-all.sh
```

## HTTP 403 — plain nginx (sem erro PHP)

Quando o navegador mostra a página padrão **403 Forbidden** com `<center>nginx</center>` (146 bytes), o **nginx bloqueou antes do PHP**. Isso **não** é open_basedir (que geraria erro PHP ou HTTP 500).

### Causa #1 (mais comum): document root sem `/public`

O aaPanel costuma gravar `root /www/wwwroot/DOMAIN;` (raiz do site). Laravel **não tem** `index.php` ali — só em `public/`. O nginx retorna **403** porque não encontra um index válido.

**Verifique:**

```bash
sudo nginx -T 2>/dev/null | grep -E 'root.*/www/wwwroot/(arrow|store|admin)\.arrow\.app\.br'
```

**Correto:** `root /www/wwwroot/DOMAIN/public;`  
**Errado:** `root /www/wwwroot/DOMAIN;`

#### Correção imediata no servidor (sem esperar git pull)

Arquivos vhost padrão do aaPanel: `/www/server/panel/vhost/nginx/DOMAIN.conf`

```bash
sudo sed -i 's|root /www/wwwroot/arrow.app.br;|root /www/wwwroot/arrow.app.br/public;|' \
  /www/server/panel/vhost/nginx/arrow.app.br.conf
sudo sed -i 's|root /www/wwwroot/store.arrow.app.br;|root /www/wwwroot/store.arrow.app.br/public;|' \
  /www/server/panel/vhost/nginx/store.arrow.app.br.conf
sudo sed -i 's|root /www/wwwroot/admin.arrow.app.br;|root /www/wwwroot/admin.arrow.app.br/public;|' \
  /www/server/panel/vhost/nginx/admin.arrow.app.br.conf
sudo nginx -t && sudo nginx -s reload
```

Ou use o script (após `git pull` / `update-repo.sh`):

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo ./fix-nginx-root.sh
```

O script também verifica extensões em `/www/server/panel/vhost/nginx/extension/DOMAIN/`.

### Diferença entre 403, 404 e open_basedir

| Resposta | Significado |
|----------|-------------|
| **403 plain nginx** | **root sem `/public`** (causa #1), permissões, ou index.php ilegível/ausente |
| **404 nginx/Laravel** | root OK mas rota/arquivo não existe, ou vendor ausente |
| **500 + open_basedir no log** | PHP rodou mas open_basedir aponta só para `public/` |

### Outras causas comuns no aaPanel

1. **Running directory errado** — Laravel precisa de `/public`, não `/` (raiz do site)
2. **Deploy com sudo** — pastas ficam `root:root` com `700`; usuário `www` não atravessa até `public/index.php`
3. **`public/index.php` ausente** — deploy não sincronizou store/admin
4. **Execute bit nas pastas pai** — `/www`, `/www/wwwroot` e a pasta do site precisam de `755`

### Diagnóstico no servidor

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo chmod +x *.sh
sudo ./diagnose-403.sh | tee /tmp/diagnose-403.log
```

Confira especialmente:

- `root` no vhost = `/www/wwwroot/DOMAIN/public`
- `ls -la .../public/index.php` existe e é `-rw-r--r-- www www`
- `sudo -u www test -r .../public/index.php` retorna OK

### Correção automática

```bash
sudo ./fix-nginx-root.sh      # document root → /public (faça primeiro)
sudo ./fix-permissions.sh
sudo ./fix-open-basedir.sh   # se ainda houver erro PHP depois do 403 sumir
```

Ou tudo de uma vez:

```bash
sudo ./fix-all.sh
```

### Correção manual no aaPanel

Para **arrow.app.br**, **store.arrow.app.br** e **admin.arrow.app.br**:

1. aaPanel → **Website** → clique no domínio
2. **Site directory** → **Running directory** = `/public`
3. Salvar
4. Se persistir: **Website** → **Config file** → confirme `root /www/wwwroot/DOMAIN/public;`

### Sintoma observado (jun/2026)

- `nginx -T` mostra `root /www/wwwroot/DOMAIN;` (sem `/public`) nos três Laravel
- **403 plain nginx** em store/admin (e às vezes arrow funciona parcialmente por rewrites)
- Após corrigir root + reload: sites respondem 302/200 do Laravel

Quando um Laravel funciona e outros não, compare `nginx -T | grep root` entre os três sites e as permissões de `public/index.php` em cada pasta.

## Solução de problemas

| Sintoma | Causa provável | Correção |
|---------|----------------|----------|
| **open_basedir** / vendor não permitido | aaPanel restringe PHP só a `public/` | `./fix-open-basedir.sh` ou ajuste manual no aaPanel (ver seção acima) |
| **404** no Laravel | open_basedir, document root errado ou `vendor/` ausente | open_basedir na raiz + Running directory = `/public` + `./fix-all.sh` |
| **git pull** com conflitos | Alterações locais no servidor | `sudo ./update-repo.sh` ou `cd /www/wwwroot/arrow-repo && sudo git fetch origin main && sudo git reset --hard origin/main` |
| **403 plain nginx** (página `<center>nginx</center>`) | **root sem `/public`** no vhost, permissões root:root, index.php ausente | `./fix-nginx-root.sh` + `./fix-permissions.sh` + Running directory = `/public` no aaPanel |
| **403** no Laravel (genérico) | Permissões ou `vendor/` ausente | `sudo ./fix-permissions.sh` + `./post-deploy.sh` |
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
