# Índices Firestore

Arquivo canônico: `firestore_indexes.json` (espelhado em `firestore.indexes.json` para o CLI).

## Índices relevantes (admin `/drivers`, seções)

Já incluídos no JSON:

| Collection | Campos |
|---|---|
| `sections` | `isActive` ASC, `order` ASC |
| `users` | `isOwner`, `role`, `sectionId`, `createdAt` DESC |
| `users` | `sectionIds` CONTAINS, `isOwner`, `role`, `createdAt` DESC |
| `provider_orders` | `provider.author` + `createdAt` / `status` / `sectionId` / `newScheduleDateTime`; `workerId` + `createdAt` |
| `providers_workers` | `providerId` + `createdAt`; `active` + `providerId`; `providerId` + `online` + `createdAt` |

Não há `firestore.rules` versionado neste repo. Não foi criado arquivo de rules (default deny em um arquivo novo quebraria customer/store/driver; allow-all abriria o banco). Trechos sugeridos para NFS-e da reserva (`provider_orders.invoices` + Storage `provider_orders/{orderId}/invoices/`): ver `firebase/rules/README.md`.

Se o console Firebase mostrar o link “create index”, abra **uma vez** e confirme, **ou** faça deploy:

```bash
cd firebase/indexes
firebase login
firebase use <SEU_PROJECT_ID>
firebase deploy --only firestore:indexes
```

A criação de índices compostos pode levar alguns minutos. Até lá, o admin usa fallback sem `orderBy` (lista funciona; o warn some após o índice ficar Ready).

## Setup inicial (se ainda não inicializou)

```bash
cd firebase/indexes
firebase init firestore
# Use firestore.indexes.json como arquivo de índices
firebase deploy --only firestore:indexes
```
