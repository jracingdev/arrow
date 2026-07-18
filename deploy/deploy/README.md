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
| `fix-open-basedir.sh` | Corrige open_basedir nos vhosts aaPanel (Laravel precisa da raiz do site) |
| `fix-user-ini.sh` | Corrige `.user.ini` imutáveis (`chattr -i` → sed → `chattr +i`) |
| `fix-open-basedir-now.sh` | Correção rápida: `fix-open-basedir` + `fix-user-ini` |
| `fix-nginx-root.sh` | Corrige document root nginx → `/public` (causa #1 de 403 plain nginx) |
| `fix-permissions.sh` | chown/chmod completo — corrige 403 nginx (www não lê public/) |
| `diagnose-403.sh` | Diagnóstico de 403 plain nginx (root, permissões, vhost) |
| `diagnose-all.sh` | Diagnóstico completo: arquivos, nginx -T, extension, .user.ini, curl, PHP |
| `fix-arrow-complete.sh` | Correção rápida: nginx root + .user.ini + permissões + composer se vendor ausente |
| `fix-all.sh` | Recuperação completa: repo + PHP + deploy + permissões + nginx root + open_basedir + check |
| `fix-firebase-config.sh` | Valida `FIREBASE_*` nos `.env`, gera `firebase-messaging-sw.js`, limpa cache config |
| `prepare-android-apps.sh` | `flutter pub get` / analyze nos 3 apps Flutter; opcional `--build-debug` / `--build-release` |
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

FIREBASE_APIKEY=<...>
FIREBASE_AUTH_DOMAIN=<...>
FIREBASE_DATABASE_URL=<...>
FIREBASE_PROJECT_ID=<...>
FIREBASE_STORAGE_BUCKET=<...>
FIREBASE_MESSAAGING_SENDER_ID=<...>
FIREBASE_APP_ID=<...>
FIREBASE_MEASUREMENT_ID=<...>
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

FIREBASE_APIKEY=<...>
FIREBASE_AUTH_DOMAIN=<...>
FIREBASE_DATABASE_URL=<...>
FIREBASE_PROJECT_ID=<...>
FIREBASE_STORAGE_BUCKET=<...>
FIREBASE_MESSAAGING_SENDER_ID=<...>
FIREBASE_APP_ID=<...>
FIREBASE_MEASUREMENT_ID=<...>
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

## 6. Nginx — document root e aaPanel

### Configurações obrigatórias no aaPanel (Website → cada domínio)

| Configuração | arrow / store / admin | lp.arrow.app.br |
|--------------|----------------------|-----------------|
| **Running directory** | `/public` | `/` (raiz) |
| **Anti-XSS attack** | **OFF** (Laravel quebra com XSS filter ativo) | conforme necessário |
| **open_basedir** (aba PHP) | `/www/wwwroot/DOMAIN/:/tmp/` (sem `/public`) | — |

> **Verificação crítica:** após qualquer alteração no painel, confirme que **arrow.app.br** aparece em `nginx -T` com `/public`:
>
> ```bash
> sudo nginx -T 2>/dev/null | grep 'root.*/www/wwwroot/arrow.app.br'
> # Esperado: root /www/wwwroot/arrow.app.br/public;
> ```
>
> Se só admin/store aparecem com `/public` e arrow não, o vhost `arrow.app.br.conf` não foi atualizado ou uma config em `extension/arrow.app.br/` sobrescreve o root.

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

## 8. Firebase — configuração dos painéis Laravel

### Como funciona

Os três painéis Laravel (**website**, **store**, **admin**) **não** leem a config Firebase do Firestore. O fluxo é:

1. **`partials/firebase-init.blade.php`** injeta `window.__firebaseConfig` via `@json(config('firebase.client'))` e chama `firebase.initializeApp()` inline (sem cookies).
2. **`public/js/jquery.validate.js`** existe apenas como fallback legado (3 linhas); **não** é mais carregado pelas views — a init acontece só no partial.
3. As views Blade (ex.: `footer.blade.php`, `layouts/app.blade.php`) usam `firebase.firestore()` no browser para ler/gravar dados (coleção `settings`, pedidos, etc.).
4. O **`SettingsController`** só renderiza telas de configuração — os valores são salvos no **Firestore pelo JavaScript**, não vêm do `.env`.

> Ter só `FIREBASE_PROJECT_ID` preenchido **não basta**. Sem **todas** as variáveis, o console mostra `No Firebase App '[DEFAULT]' has been created` e erros em cascata (`collection` undefined).

### Variáveis `.env` obrigatórias (os 3 painéis)

| Variável | Exemplo | Onde obter no Console Firebase |
|----------|---------|----------------------------------|
| `FIREBASE_APIKEY` | `AIzaSy...` | Project settings → General → Your apps → Web app → `apiKey` |
| `FIREBASE_AUTH_DOMAIN` | `j-arrow.firebaseapp.com` | mesmo bloco → `authDomain` |
| `FIREBASE_DATABASE_URL` | `https://j-arrow-default-rtdb.firebaseio.com` | Realtime Database (se usado) ou URL do projeto |
| `FIREBASE_PROJECT_ID` | `j-arrow` | `projectId` |
| `FIREBASE_STORAGE_BUCKET` | `j-arrow.appspot.com` | `storageBucket` |
| `FIREBASE_MESSAAGING_SENDER_ID` | `123456789` | `messagingSenderId` (grafia original do eMart: **MESSAAGING**) |
| `FIREBASE_APP_ID` | `1:123:web:abc` | `appId` |
| `FIREBASE_MEASUREMENT_ID` | `G-XXXX` | `measurementId` (Analytics; pode ficar vazio em dev, mas preencha em produção) |

**Passos no Console Firebase:**

1. Acesse [console.firebase.google.com](https://console.firebase.google.com) → projeto **j-arrow** (ou o seu).
2. Ícone de engrenagem → **Project settings** → aba **General**.
3. Em **Your apps**, selecione o app Web (ou crie um: **Add app** → Web).
4. Copie o objeto `firebaseConfig` e preencha cada `FIREBASE_*` no `.env` dos **três** sites.

Repita o mesmo bloco nos três `.env`:

- `/www/wwwroot/arrow.app.br/.env`
- `/www/wwwroot/store.arrow.app.br/.env`
- `/www/wwwroot/admin.arrow.app.br/.env`

### Após editar o `.env`

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo chmod +x *.sh   # necessário se ./fix-all.sh der "Permission denied"
sudo ./fix-firebase-config.sh
```

Ou manualmente em cada painel:

```bash
for site in arrow.app.br store.arrow.app.br admin.arrow.app.br; do
  cd /www/wwwroot/$site
  php artisan config:clear
  php artisan config:cache
done
```

> **Importante:** após `config:cache`, o Laravel usa os valores do `.env` cacheados. Sempre rode `config:clear` antes de alterar Firebase no `.env`.

### `firebase-messaging-sw.js` — obrigatório ou opcional?

| Contexto | Necessário? |
|----------|-------------|
| **Website** (`arrow.app.br`) — push notifications web (FCM) | **Sim** — deve existir em `public/firebase-messaging-sw.js` |
| **Store / Admin** | **Não** — esses painéis não registram service worker de messaging |
| **Login e Firestore básico** | **Opcional** para o SW — mas **obrigatório** preencher todas as `FIREBASE_*` no `.env` |

O arquivo é um **service worker** (não um `<script>` comum). O SDK do Firebase busca automaticamente `https://seu-dominio/firebase-messaging-sw.js`. O script `deploy/fix-firebase-config.sh` gera esse arquivo a partir do `.env` do website usando o template em `deploy/templates/`.

Se aparecer **404** em `/firebase-messaging-sw.js`, rode:

```bash
sudo ./fix-firebase-config.sh
```

### Verificação rápida

```bash
./check-env.sh
curl -I https://arrow.app.br/firebase-messaging-sw.js   # deve retornar 200
```

No browser (F12 → Console), após corrigir o `.env` e limpar cache, **não** deve aparecer `No Firebase App '[DEFAULT]' has been created`.

### Erros comuns no console do browser (pós-deploy)

| Erro | Tipo | Correção |
|------|------|----------|
| `jquery.cookie.js` / `$.decrypt` / `reading 'length'` | **Cache ou deploy antigo** | O init Firebase **não** usa mais `$.decrypt`. Confirme que `partials/firebase-init.blade.php` está implantado e que o HTML da página contém `window.__firebaseConfig`. Rode `./full-deploy.sh` e limpe cache do browser. |
| `Firestore: permission-denied` / billing | **GCP / Firebase (não é código)** | Ative faturamento no projeto **j-arrow**: [Enable billing](https://console.developers.google.com/billing/enable?project=j-arrow). Sem billing, Firestore recusa leituras mesmo com `initializeApp` OK. |
| `[ROCKET LOADER] Activator script doesn't have settings` | **Artefato legado Cloudflare no HTML** | O `rocket-loader.min.js` era **hardcoded** em `footer.blade.php` e páginas estáticas (signup/terms/faq), copiado de um export Cloudflare — **não** vem do aaPanel. Foi removido do monorepo. Se ainda aparecer, o deploy não sincronizou ou há cache. |
| `CollectionReference.doc() empty path` em `/set-location` | **Cookie `section_id` vazio** | Na página de localização o usuário ainda não escolheu seção; `footer.blade.php` agora só chama `.doc(section_id)` quando o cookie existe. |

#### Verificar Firebase init no servidor

Após `./deploy.sh` ou `./full-deploy.sh`, confirme que o HTML inclui `window.__firebaseConfig` (não depende mais de `jquery.validate.js`):

```bash
curl -s https://arrow.app.br/set-location | grep -o 'window.__firebaseConfig' | head -1
curl -s https://store.arrow.app.br/login | grep -o 'window.__firebaseConfig' | head -1
curl -s https://admin.arrow.app.br/login | grep -o 'window.__firebaseConfig' | head -1
```

O arquivo `public/js/jquery.validate.js` (se ainda existir no servidor) deve ser apenas fallback de 3 linhas — **sem** `$.decrypt`:

```bash
curl -s https://arrow.app.br/js/jquery.validate.js
```

Esperado:

```js
if (typeof firebase !== 'undefined' && window.__firebaseConfig && window.__firebaseConfig.apiKey && !firebase.apps.length) {
    firebase.initializeApp(window.__firebaseConfig);
}
```

Se ainda aparecer `$.decrypt($.cookie('XSRF-TOKEN-AK'))`, o deploy não sincronizou `public/js/` ou o browser está em cache.

#### Rocket Loader (removido do monorepo)

O `rocket-loader.min.js` era um script **Cloudflare** copiado para `web/website/public/js/` e referenciado em Blade (`footer.blade.php`, signup, terms, faq). **Não** é injetado pelo aaPanel nem exige Cloudflare no DNS. Foi removido — após deploy, `curl -s https://arrow.app.br/ | grep rocket-loader` não deve retornar nada.

Se usar Cloudflare no futuro, desative Rocket Loader em Speed → Optimization e marque scripts Firebase com `data-cfasync="false"`.

#### Firestore — billing obrigatório

O projeto Firebase **j-arrow** precisa de conta de faturamento GCP vinculada para Firestore em produção:

1. Abra [console.developers.google.com/billing/enable?project=j-arrow](https://console.developers.google.com/billing/enable?project=j-arrow)
2. Vincule uma conta de faturamento (plano Spark gratuito não cobre uso além dos limites; muitos projetos exigem Blaze para Firestore)
3. No Firebase Console → Firestore → confirme que o banco está criado e as regras permitem leitura conforme sua app

Isso **não** se corrige no código do monorepo.

## 9. Firebase Functions (opcional)

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

### Correção imediata no servidor (cole agora)

Após conflitos de `git pull`, resete o repo e corrija `.user.ini` + vhosts:

```bash
cd /www/wwwroot/arrow-repo
sudo git fetch origin main && sudo git reset --hard origin/main
cd deploy && sudo chmod +x *.sh && sudo ./fix-open-basedir-now.sh
```

Ou só o `.user.ini` (se o vhost já estiver correto):

```bash
cd /www/wwwroot/arrow-repo
sudo git fetch origin main && sudo git reset --hard origin/main
cd deploy && sudo chmod +x *.sh && sudo ./fix-user-ini.sh
```

Scripts individuais:

```bash
cd /www/wwwroot/arrow-repo/deploy
sudo ./fix-open-basedir.sh   # vhosts aaPanel + pools PHP-FPM
sudo ./fix-user-ini.sh       # .user.ini imutáveis (chattr -i → sed → chattr +i)
```

Recuperação completa (recomendado se vários problemas ao mesmo tempo):

```bash
cd /www/wwwroot/arrow-repo
sudo git fetch origin main && sudo git reset --hard origin/main
cd deploy && sudo chmod +x *.sh && sudo ./fix-all.sh
```

### Fallback manual no aaPanel (se `chattr -i` falhar)

O aaPanel marca `.user.ini` como imutável. Se o script mostrar `chattr -i falhou`:

1. aaPanel → **Website** → clique no domínio (ex.: `admin.arrow.app.br`)
2. Aba **PHP** → campo **open_basedir**
3. Altere de `/www/wwwroot/admin.arrow.app.br/public/:/tmp/` para `/www/wwwroot/admin.arrow.app.br/:/tmp/`
4. Repita para **store.arrow.app.br** e **arrow.app.br**
5. **App Store** → **PHP 8.2** → **Restart**

Via SSH (como root), se tiver acesso direto:

```bash
chattr -i /www/wwwroot/admin.arrow.app.br/public/.user.ini
sed -i 's|/public/:/tmp/|/:/tmp/|g' /www/wwwroot/admin.arrow.app.br/public/.user.ini
chattr +i /www/wwwroot/admin.arrow.app.br/public/.user.ini
```

Repita para `store.arrow.app.br` e `arrow.app.br` (ambos os caminhos: `DOMAIN/.user.ini` e `DOMAIN/public/.user.ini`).

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
sudo ./diagnose-all.sh | tee /tmp/diagnose-all.log
# ou foco em 403:
sudo ./diagnose-403.sh | tee /tmp/diagnose-403.log
```

### Correção rápida (404/403 sem redeploy completo)

```bash
cd /www/wwwroot/arrow-repo
sudo git fetch origin main && sudo git reset --hard origin/main
cd deploy && sudo chmod +x *.sh && sudo ./fix-arrow-complete.sh
```

Confira especialmente:

- `root` no vhost = `/www/wwwroot/DOMAIN/public`
- `ls -la .../public/index.php` existe e é `-rw-r--r-- www www`
- `sudo -u www test -r .../public/index.php` retorna OK

### Correção automática

```bash
sudo ./fix-nginx-root.sh         # document root → /public (faça primeiro)
sudo ./fix-permissions.sh
sudo ./fix-open-basedir-now.sh   # open_basedir vhosts + .user.ini (se erro PHP depois do 403 sumir)
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
- **grep só mostra admin/store com /public** — `arrow.app.br` ausente = vhost principal não patchado
- `sed` com glob falha sem bash: use `sudo bash -c 'for f in ...'` ou `./fix-nginx-root.sh`
- Config em `extension/DOMAIN/*.conf` pode sobrescrever root de volta para raiz do site
- Conflito: aaPanel "Running directory" + sed manual podem gerar `/public/public` (script corrige)
- **403 plain nginx** em store/admin; **404** quando root errado ou vendor ausente
- Após corrigir root + reload: sites respondem 302/200 do Laravel

Quando um Laravel funciona e outros não, compare `nginx -T | grep root` entre os três sites e as permissões de `public/index.php` em cada pasta.

## Solução de problemas

| Sintoma | Causa provável | Correção |
|---------|----------------|----------|
| **open_basedir** / vendor não permitido | aaPanel restringe PHP só a `public/` (`.user.ini` ou vhost) | `sudo ./fix-open-basedir-now.sh` ou `./fix-user-ini.sh` + fallback manual no aaPanel |
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
