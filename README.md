# Arrow (eMart)

Monorepo da plataforma **Arrow** — baseada no eMart 6.3 — com painéis web Laravel, landing page estática, apps Flutter e recursos Firebase.

**Repositório:** https://github.com/jracingdev/arrow.git

## Estrutura do projeto

```
arrow/
├── README.md
├── .gitignore
├── database/              # Dumps SQL (desenvolvimento/importação inicial)
│   ├── emart_admin.sql
│   ├── emart_store.sql
│   └── emart_website.sql
├── web/
│   ├── landing/           # Landing page estática (HTML)
│   ├── website/           # Painel do cliente (Laravel 10)
│   ├── store/             # Painel de lojistas (Laravel 10)
│   └── admin/             # Painel administrativo (Laravel 10)
├── apps/
│   ├── customer/          # App Flutter — cliente
│   ├── store/             # App Flutter — lojista
│   └── driver/            # App Flutter — entregador
├── firebase/
│   ├── functions/         # Cloud Functions (rastreamento de pedidos)
│   ├── indexes/           # Índices Firestore
│   ├── import-export/     # Coleções para import/export
│   └── demo-auth-import/  # Script de importação de usuários demo
├── docs/                  # Documentação em PDF
└── deploy/                # Guias e scripts de deploy (aaPanel/Nginx)
```

## Mapeamento de produção (aaPanel)

| Domínio | Pasta no servidor | Caminho no monorepo | Document root Nginx |
|---------|-------------------|---------------------|---------------------|
| https://arrow.app.br | `arrow.app.br` | `web/website` | `public/` |
| https://lp.arrow.app.br | `lp.arrow.app.br` | `web/landing` | raiz da pasta |
| https://store.arrow.app.br | `store.arrow.app.br` | `web/store` | `public/` |
| https://admin.arrow.app.br | `admin.arrow.app.br` | `web/admin` | `public/` |

**Servidor:** `56.125.221.106` · PHP 8.2 · Nginx 1.24 · MySQL 8.0.45 · SSL ativo

### Bancos MySQL em produção

| Painel | Banco | Usuário | Host |
|--------|-------|---------|------|
| Website | `arrow_website_db` | `arrow_website_adm` | `localhost` |
| Store | `arrow_store_db` | `arrow_store_adm` | `localhost` |
| Admin | `arrow_admin_db` | `arrow_admin_adm` | `localhost` |
| Landing | — | — | sem banco |

> As senhas dos bancos **não** ficam no repositório. Configure-as apenas no `.env` de cada painel no servidor.

## Pré-requisitos

- PHP 8.1+ com extensões: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Composer 2.x
- MySQL 8.x
- Node.js 18+ (Firebase Functions)
- Flutter SDK (apps mobile)
- Firebase CLI (`npm install -g firebase-tools`)

## Configuração local — painéis Laravel

Para cada painel em `web/admin`, `web/store` e `web/website`:

```bash
cd web/<painel>
composer install
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com credenciais locais. Os arquivos `.env.example` já trazem os nomes de banco e `APP_URL` de produção como referência.

```bash
php artisan migrate          # se necessário
php artisan storage:link     # se necessário
chmod -R 775 storage bootstrap/cache   # Linux
```

### Website (`web/website`)

- `APP_URL=https://arrow.app.br`
- `DB_DATABASE=arrow_website_db`
- `DB_USERNAME=arrow_website_adm`

### Store (`web/store`)

- `APP_URL=https://store.arrow.app.br`
- `DB_DATABASE=arrow_store_db`
- `DB_USERNAME=arrow_store_adm`

### Admin (`web/admin`)

- `APP_URL=https://admin.arrow.app.br`
- `DB_DATABASE=arrow_admin_db`
- `DB_USERNAME=arrow_admin_adm`

Todos os painéis Laravel usam variáveis `FIREBASE_*` — preencha com os dados do Console Firebase.

## Landing page (`web/landing`)

Site estático HTML. Não requer Composer nem banco de dados.

- **Produção:** document root = raiz de `web/landing`
- Basta servir os arquivos via Nginx/Apache

## Importação dos bancos de dados

Os dumps em `database/` são os originais do eMart. Em produção, use os bancos já criados no aaPanel (`arrow_*_db`). Para ambiente local:

```bash
mysql -u root -p -e "CREATE DATABASE arrow_website_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p arrow_website_db < database/emart_website.sql

mysql -u root -p -e "CREATE DATABASE arrow_store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p arrow_store_db < database/emart_store.sql

mysql -u root -p -e "CREATE DATABASE arrow_admin_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p arrow_admin_db < database/emart_admin.sql
```

Ajuste nomes de banco/usuário no `.env` conforme seu ambiente.

## Firebase

### Cloud Functions (`firebase/functions`)

```bash
cd firebase/functions
npm install
firebase login
firebase deploy --only functions
```

### Índices Firestore (`firebase/indexes`)

Importe `firestore_indexes.json` no Console Firebase ou via CLI.

### Import/Export de coleções (`firebase/import-export`)

Siga o `README.md` da pasta. **Não commite** `credentials.json`.

### Usuários demo (`firebase/demo-auth-import`)

```bash
cd firebase/demo-auth-import
npm install
# Coloque serviceAccountKey.json localmente (não versionado)
node import-user.js
```

## Apps Flutter (`apps/`)

| App | Pasta | Público |
|-----|-------|---------|
| Cliente | `apps/customer` | Consumidores finais |
| Lojista | `apps/store` | Vendedores/lojas |
| Entregador | `apps/driver` | Motoristas/entregadores |

```bash
cd apps/customer   # ou store / driver
flutter pub get
flutter run
```

Configure `google-services.json` (Android) e credenciais Firebase localmente — arquivos sensíveis estão no `.gitignore`.

Documentação detalhada: `docs/eMart App Documentation.pdf`

## Deploy em produção

Consulte o guia completo em **[deploy/README.md](deploy/README.md)**.

Resumo:

1. `git clone https://github.com/jracingdev/arrow.git` no servidor
2. Sincronize cada pasta `web/*` para o diretório aaPanel correspondente
3. `composer install --no-dev` em cada painel Laravel
4. Configure `.env` com credenciais de produção (senhas fora do Git)
5. `php artisan config:cache` e `php artisan route:cache`

## Segurança

- Nunca commite `.env`, senhas de banco, `credentials.json` ou `serviceAccountKey.json`
- `vendor/` e `node_modules/` são ignorados — rode `composer install` após clonar
- Em produção: `APP_DEBUG=false`, `APP_ENV=production`

## Licença

Código base eMart — consulte a documentação original em `docs/`.
