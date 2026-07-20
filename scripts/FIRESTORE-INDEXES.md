# Firestore Indexes — Arrow

Arquivo canônico: [`firebase/indexes/firestore.indexes.json`](../firebase/indexes/firestore.indexes.json)

## Deploy

Na raiz do monorepo (ou pasta com `firebase.json` apontando para esse arquivo):

```bash
firebase deploy --only firestore:indexes
```

Confirme no Console se o projeto Firebase está correto (`firebase use`).

## Status no Console

Após o deploy, índices novos ficam em **Building** por alguns minutos/horas. Enquanto isso:

- O web usa **fallback** em runtime (ver `web/*/public/js/firestore-safe.js` e `try/catch` em queries compostas).
- Erros `failed-precondition` / link “create index” no browser podem aparecer — o fallback evita crash.

Link direto (substitua `PROJECT_ID`):

`https://console.firebase.google.com/project/PROJECT_ID/firestore/indexes`

## Índices críticos (auditoria Jul/2026)

| Collection | Campos | Uso |
|---|---|---|
| `sections` | `isActive` + `order` | Menus, login, listagens |
| `users` | `role` + `vendorID` + `isActive` | Entregadores no pedido |
| `users` | `role` + `sectionId` + `firstName` | Owners ao criar loja |
| `users` | `role` + `phoneNumber` (+ `active`) | Login telefone store/website |
| `vendors` | `section_id` + `reviewsSum` | Home multivendor popular |
| `vendor_products` | `vendorID` + `publish` + `createdAt` | Produtos relacionados |
| `vendor_categories` | `section_id` + `publish` | Categorias por seção |

## Se o Console pedir índice manual

1. Abra o link do erro no DevTools (Firebase monta a URL com os campos).
2. Ou copie os campos e adicione em `firestore.indexes.json` no mesmo formato (inclua `__name__` e `density: SPARSE_ALL` como nos demais).
3. Rode `firebase deploy --only firestore:indexes` de novo.

## Helpers no código

- `ArrowFirestore.fetchActiveSectionsOrdered()` — `isActive` + `orderBy('order')` com fallback
- `ArrowFirestore.isValidDocId()` / `safeDocGet()` — evita `FirebaseError: Invalid document reference. Document references must have an even number of segments` / empty path
- `ArrowFirestore.applyGlobalOrBrCountry()` — DDI padrão `+55`
