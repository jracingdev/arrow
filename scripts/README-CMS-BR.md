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
| `scripts/update-onboarding-ptbr.js` | Onboarding residual pt-BR (inclui 6 títulos EN) |
| `scripts/list-sections.js` | Diagnóstico das seções |

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
