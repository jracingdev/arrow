# Regras Firestore / Storage — NFS-e da reserva

Este repositório **não versiona** `firestore.rules` nem `storage.rules` para deploy
(um arquivo novo com default-deny quebraria customer/store/driver; allow-all
abriria o banco). Aplique no **Firebase Console** (`j-arrow`) os trechos abaixo,
sem substituir o restante das rules de produção.

## Contrato

- Coleção: `provider_orders`
- Campo: `invoices` — array de `{ type: 'nfs-e', url, fileName, uploadedAt, uploadedBy }`
- Storage: `provider_orders/{orderId}/invoices/{uuid}.{pdf|jpg|jpeg|png|webp}`
- Quem escreve: prestador dono do pedido (`provider.author`)
- Quem lê: cliente do pedido (`authorID` / `author.id`) e o prestador
- Upload de app: status `Order Ongoing`, `In Transit` ou `Order Completed`
- Tamanho: até ~10 MB; PDF ou imagem

## Firestore (trecho sugerido)

Ajuste ao redor da rule já existente de `provider_orders`. Não publique um
arquivo que dê `allow read, write: if true` em `/`.

```
match /provider_orders/{orderId} {
  function isProviderAuthor() {
    return request.auth != null
      && resource.data.provider.author == request.auth.uid;
  }
  function isOrderCustomer() {
    return request.auth != null
      && (resource.data.authorID == request.auth.uid
          || resource.data.author.id == request.auth.uid);
  }

  allow read: if isProviderAuthor() || isOrderCustomer();

  // Prestador atualiza status, workerId, invoices, etc.
  allow update: if isProviderAuthor();
}
```

O cliente **não** deve escrever `invoices`. Se a rule de `update` do cliente
já existir (cancelar pedido, cupom), restrinja com
`!request.resource.data.diff(resource.data).affectedKeys().hasAny(['invoices'])`.

## Storage (trecho sugerido)

```
match /provider_orders/{orderId}/invoices/{fileName} {
  function orderDoc() {
    return firestore.get(/databases/(default)/documents/provider_orders/$(orderId)).data;
  }
  function isProviderAuthor() {
    return request.auth != null && orderDoc().provider.author == request.auth.uid;
  }
  function isOrderCustomer() {
    return request.auth != null
      && (orderDoc().authorID == request.auth.uid
          || orderDoc().author.id == request.auth.uid);
  }

  allow read: if isProviderAuthor() || isOrderCustomer();
  allow write: if isProviderAuthor()
    && request.resource.size < 10 * 1024 * 1024
    && request.resource.contentType.matches('application/pdf|image/.*');
}
```

Se o Storage ainda estiver aberto (`allow read, write: if request.auth != null`),
restrinja **só este prefixo** primeiro; não altere paths de chat/imagens sem
testar os outros apps.

## Console

1. Firebase Console → Firestore → Rules → incorporar o trecho (não colar um
   arquivo completo deny-all).
2. Firebase Console → Storage → Rules → idem para o path
   `provider_orders/{orderId}/invoices/{fileName}`.
3. Sem deploy deste repo (`firebase deploy --only firestore:rules` / `storage`
   não está configurado aqui de propósito).
