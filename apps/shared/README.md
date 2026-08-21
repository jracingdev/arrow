# Arrow shared config

Pacote Dart local usado pelos apps Flutter (`customer`, `store`, `driver`, `provider`).

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
| Prestador | `br.app.arrow.provider` |

App ID Android do prestador: `1:661081769489:android:e3046689b04067d2a4d3b0`.

SHA para colar no app Android **novo** (`br.app.arrow.provider`):

- Debug SHA-1: `E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC`
- Release SHA-1: `1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD`

Passo a passo: `firebase/android/README.md`.

## Firebase após rename de package/bundle

Depois de alterar `applicationId` / `PRODUCT_BUNDLE_IDENTIFIER`, **é obrigatório** re-registrar os apps no Firebase Console (projeto `j-arrow`):

1. Firebase Console → Project settings → Your apps → Add app (Android/iOS) com os novos IDs acima (ou editar/recriar se IDs antigos ainda existirem).
2. Baixar novos arquivos e colocar nos caminhos:
   - Android: `apps/<app>/android/app/google-services.json`
   - iOS: `apps/<app>/ios/Runner/GoogleService-Info.plist`
3. Regenerar `firebase_options.dart` com FlutterFire CLI (`flutterfire configure`) apontando para os novos apps, ou atualizar `appId`/`iosBundleId` manualmente.
4. **Não commitar** `google-services.json` / `GoogleService-Info.plist` (estão no `.gitignore`).

Sem esse passo, Auth, FCM e Crashlytics quebram no build/runtime.
