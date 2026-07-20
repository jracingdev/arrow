# CMS BR — scripts Firestore (Arrow)

Scripts para aplicar no **servidor** com `firebase/import-export/credentials.json` (service account real). No repo local o arquivo pode estar vazio; **não commitar credenciais reais**.

## Pré-requisito

```bash
cd firebase/import-export
npm i
```

## Rodar tudo (recomendado)

```bash
# order 0..n + termos/privacidade + contato/BRL/pagamentos + onboarding
node ../../scripts/run-cms-br-all.js

# idem + ativar módulos BR (Restaurants, Food Grocery, Cab, Delivery Express, Rental, E-commerce, On Demand)
node ../../scripts/run-cms-br-all.js --activate-br

# ativar BR e desativar as demais seções
node ../../scripts/run-cms-br-all.js --activate-br --deactivate-others

# só simular
node ../../scripts/run-cms-br-all.js --dry-run
```

Atalhos npm (a partir de `firebase/import-export`):

```bash
npm run cms:all
npm run cms:all:br
```

## Scripts individuais

| Script | Função |
|--------|--------|
| `scripts/backfill-sections-br.js` | Backfill `order` 0..n; `--activate-br` |
| `scripts/seed-terms-privacy-lgpd.js` | `settings/termsAndConditions` + `privacyPolicy` (HTML pt-BR LGPD, placeholders `[RAZAO_SOCIAL]` `[EMAIL_CONTATO]` `[CNPJ]`) |
| `scripts/seed-contact-payments-br.js` | ContactUs Brazil, `defaultCountryCode=+55`, desliga Razorpay/PayStack/Xendit/Stripe demo, Mercado Pago enableável, currency BRL |
| `scripts/seed-document-verification-settings.js` | Cria `settings/document_verification_settings` (defaults eMart/`collections.json`: store/driver/owner = false). Só cria se ausente; `--force` sobrescreve |
| `scripts/update-onboarding-ptbr.js` | Onboarding residual pt-BR (inclui 6 títulos EN) |
| `scripts/list-sections.js` | Diagnóstico das seções |

### Document verification settings

```bash
node ../../scripts/seed-document-verification-settings.js
# node ../../scripts/seed-document-verification-settings.js --dry-run
# node ../../scripts/seed-document-verification-settings.js --force
```

Remove o log `document_verification_settings document does not exist` no login/register da store.

## Firebase Auth — Authorized domains

O erro `auth/unauthorized-domain` (ex.: login em `store.arrow.app.br`) ocorre porque o domínio **não está** na lista de Authorized domains do projeto Firebase.

No [Firebase Console](https://console.firebase.google.com/) → projeto Arrow → **Authentication** → **Settings** → **Authorized domains**, adicione:

| Domínio | Uso |
|---------|-----|
| `arrow.app.br` | site / app web principal |
| `store.arrow.app.br` | painel loja (store) |
| `admin.arrow.app.br` | painel admin |
| `lp.arrow.app.br` | landing (se usar Firebase Auth) |
| `localhost` | dev local (costuma já existir) |

Não é necessário alterar código nem fazer `firebase login` para isso — só a lista no Console.

### Termos com dados reais (opcional)

```bash
node ../../scripts/seed-terms-privacy-lgpd.js \
  --razao="Razão Social Real LTDA" \
  --email="privacidade@seudominio.com" \
  --cnpj="00.000.000/0001-00"
```

### Pagamentos + desativar USD demo

```bash
node ../../scripts/seed-contact-payments-br.js --deactivate-usd
```

## Seed `collections.json`

O seed em `firebase/import-export/collections.json` também foi alinhado (BRL, ContactUs/Brazil, `+55`, gateways demo off, termos/privacidade LGPD) para quem reimporta via `firestore-import`. Em produção já existente, preferir os scripts de merge acima (não precisam reimportar o JSON inteiro).
