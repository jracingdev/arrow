# Arrow shared config

Pacote Dart local usado pelos três apps Flutter (`customer`, `store`, `driver`).

## URLs de produção

| Constante | Valor | Uso |
|-----------|-------|-----|
| `kAdminApiBaseUrl` | `https://admin.arrow.app.br/` | `GlobalURL` — pagamentos, webhooks, APIs server-side |
| `kWebsiteBaseUrl` | `https://arrow.app.br` | Site cliente (também via Firestore) |
| `kStorePanelBaseUrl` | `https://store.arrow.app.br` | Painel lojista web |
| `kFirebaseProjectId` | `j-arrow` | Firebase / FlutterFire CLI |

## Android package names

| App | `applicationId` |
|-----|-------------------|
| Cliente | `com.emart.customer` |
| Lojista | `com.emart.store` |
| Entregador | `com.emart.driver` |
