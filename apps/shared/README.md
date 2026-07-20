# Arrow shared config

Pacote Dart local usado pelos três apps Flutter (`customer`, `store`, `driver`).

## URLs de produção

| Constante | Valor | Uso |
|-----------|-------|-----|
| `kAdminApiBaseUrl` | `https://admin.arrow.app.br/` | `GlobalURL` — pagamentos, webhooks, APIs server-side |
| `kWebsiteBaseUrl` | `https://arrow.app.br` | Site cliente (também via Firestore) |
| `kStorePanelBaseUrl` | `https://store.arrow.app.br` | Painel lojista web |
| `kFirebaseProjectId` | `j-arrow` | Firebase / FlutterFire CLI |

## Package / Bundle IDs

| App | Android `applicationId` / iOS bundle ID |
|-----|------------------------------------------|
| Cliente | `br.app.arrow.customer` |
| Lojista | `br.app.arrow.store` |
| Entregador | `br.app.arrow.driver` |

## Firebase após rename de package/bundle

Depois de alterar `applicationId` / `PRODUCT_BUNDLE_IDENTIFIER`, **é obrigatório** re-registrar os apps no Firebase Console (projeto `j-arrow`):

1. Firebase Console → Project settings → Your apps → Add app (Android/iOS) com os novos IDs acima (ou editar/recriar se IDs antigos ainda existirem).
2. Baixar novos arquivos e colocar nos caminhos:
   - Android: `apps/<app>/android/app/google-services.json`
   - iOS: `apps/<app>/ios/Runner/GoogleService-Info.plist`
3. Regenerar `firebase_options.dart` com FlutterFire CLI (`flutterfire configure`) apontando para os novos apps, ou atualizar `appId`/`iosBundleId` manualmente.
4. **Não commitar** `google-services.json` / `GoogleService-Info.plist` (estão no `.gitignore`).

Sem esse passo, Auth, FCM e Crashlytics quebram no build/runtime.
