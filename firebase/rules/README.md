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

## Documentos KYC (`documents_verify`)

Clientes **não** leem documentos de terceiros. Arquivos (antecedentes, RG, CRLV)
ficam só no admin e no dono do uid. O app do cliente usa apenas
`users.isDocumentVerify` para o selo público.

### Firestore

```
match /documents_verify/{uid} {
  function isOwner() {
    return request.auth != null && request.auth.uid == uid;
  }
  function isAdmin() {
    return request.auth != null
      && get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == 'admin';
  }

  allow read: if isOwner() || isAdmin();

  // Solicitante envia/substitui arquivos e volta status para pending.
  allow create, update: if isOwner()
    && request.resource.data.id == uid
    && request.resource.data.pending == true
    && request.resource.data.isDocumentVerify == null;

  // Admin aprova/recusa (status, rejectReason, pending).
  allow update: if isAdmin();
  allow delete: if isAdmin();
}
```

Não publique um arquivo deny-all. Se `users` já restringe `role`, ajuste
`isAdmin()` ao campo que o painel Laravel usa.

### Storage

Path existente do motorista: `documents_verify/{uid}/{docId}_{front|back}.jpg`
(prestador usa o mesmo prefixo). Driver legado também grava em
`driverDocument/{uid}/`.

```
match /documents_verify/{uid}/{fileName} {
  allow read, write: if request.auth != null && request.auth.uid == uid;
  allow read: if request.auth != null
    && get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == 'admin';
}

match /driverDocument/{uid}/{fileName} {
  allow read, write: if request.auth != null && request.auth.uid == uid;
  allow read: if request.auth != null
    && get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == 'admin';
}
```

O cliente **nunca** deve ter `allow read` nesses paths.

## Denúncias e SOS (`complaints` / `SOS`)

Coleções já usadas nas corridas. Prestador e cliente sob demanda gravam no
mesmo path (`complaints/{orderId}_{uid}`, `SOS/{orderId}_{uid}`). Cab legado
usa id automático e os campos `customerId` / `driverId`. Aplique no Console
**sem** substituir o restante das rules.

### Contrato

- Quem cria: usuário autenticado que é parte do pedido
  (`provider_orders`: `authorID` / `author.id` / `provider.author` / `workerId`;
  `rides`: `authorID` / `driverId`)
- Status inicial do app: `Initiated`
- Quem lê: o reporter (`reporterId`); no legado de corrida, `customerId` ou
  `driverId`. Sem leitura pública de denúncias de terceiros
- Quem atualiza `status` (`Under Investigation` / `Resolved` / `Dismissed`),
  `adminNote`, `action`: só admin/backend
- Apps sob demanda leem o próprio doc por id (`{orderId}_{uid}`). Query
  `where('orderId' == …)` só funciona se **todos** os docs retornados forem
  legíveis para o uid

### Firestore (trecho sugerido)

Reutilize `isAdmin()` se a seção KYC acima já estiver colada.

```
function isAdmin() {
  return request.auth != null
    && get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == 'admin';
}

function onDemandOrder(orderId) {
  return get(/databases/$(database)/documents/provider_orders/$(orderId)).data;
}

function rideOrder(orderId) {
  return get(/databases/$(database)/documents/rides/$(orderId)).data;
}

function isOnDemandParty(orderId) {
  return exists(/databases/$(database)/documents/provider_orders/$(orderId))
    && (
      onDemandOrder(orderId).authorID == request.auth.uid
      || onDemandOrder(orderId).author.id == request.auth.uid
      || onDemandOrder(orderId).provider.author == request.auth.uid
      || onDemandOrder(orderId).workerId == request.auth.uid
    );
}

function isRideParty(orderId) {
  return exists(/databases/$(database)/documents/rides/$(orderId))
    && (
      rideOrder(orderId).authorID == request.auth.uid
      || rideOrder(orderId).driverId == request.auth.uid
    );
}

function isOrderParty(orderId) {
  return request.auth != null
    && (isOnDemandParty(orderId) || isRideParty(orderId));
}

match /complaints/{id} {
  function isReporter() {
    return request.auth != null && resource.data.reporterId == request.auth.uid;
  }
  function isLegacyCabParty() {
    return request.auth != null
      && (resource.data.customerId == request.auth.uid
          || resource.data.driverId == request.auth.uid);
  }

  allow create: if isOrderParty(request.resource.data.orderId)
    && request.resource.data.status == 'Initiated'
    && (
      request.resource.data.reporterId == request.auth.uid
      || request.resource.data.customerId == request.auth.uid
      || request.resource.data.driverId == request.auth.uid
    );

  allow read: if isAdmin() || isReporter() || isLegacyCabParty();

  // Reporter pode reenviar SOS (set+merge) sem mudar status.
  allow update: if isReporter()
    && request.resource.data.status == resource.data.status
    && request.resource.data.reporterId == resource.data.reporterId
    && request.resource.data.orderId == resource.data.orderId;

  allow update, delete: if isAdmin();
}

match /SOS/{id} {
  function isReporter() {
    return request.auth != null && resource.data.reporterId == request.auth.uid;
  }

  allow create: if isOrderParty(request.resource.data.orderId)
    && request.resource.data.status == 'Initiated'
    && (
      !('reporterId' in request.resource.data)
      || request.resource.data.reporterId == request.auth.uid
    );

  allow read: if isAdmin()
    || isReporter()
    || (!('reporterId' in resource.data)
        && isOrderParty(resource.data.orderId));

  allow update: if isReporter()
    && request.resource.data.status == resource.data.status
    && request.resource.data.orderId == resource.data.orderId;

  allow update, delete: if isAdmin();
}
```

Não publique um arquivo deny-all. Se `users.role` não for `admin` no painel
Laravel, ajuste `isAdmin()` ao campo que o Console já usa. Parcel/rental:
espelhe `isRideParty` em `parcel_orders` / `rental_orders` se essas denúncias
existirem em produção.

### Console

1. Firebase Console → Firestore → Rules → incorporar o trecho (não colar um
   arquivo completo deny-all).
2. Sem deploy deste repo (`firebase deploy --only firestore:rules` não está
   configurado aqui de propósito).
