# Índices Firestore

Arquivo canônico: `firestore_indexes.json` (espelhado em `firestore.indexes.json` para o CLI).

## Índices relevantes (admin `/drivers`, seções)

Já incluídos no JSON:

| Collection | Campos |
|---|---|
| `sections` | `isActive` ASC, `order` ASC |
| `users` | `isOwner`, `role`, `sectionId`, `createdAt` DESC |
| `users` | `sectionIds` CONTAINS, `isOwner`, `role`, `createdAt` DESC |

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
