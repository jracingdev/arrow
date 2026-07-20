# Firebase SHA fingerprints — Arrow Android (j-arrow)

**Erro atual:** `DEVELOPER_ERROR` / `ApiException: 10` ao tocar “Logar com Google”.

Causa: o SHA-1 da keystore que assina o APK **não** está cadastrado nos apps Android do Firebase **j-arrow**, e/ou o `google-services.json` ainda tem `oauth_client: []`.

## Keystore real dos APKs instalados

Os builds release usam `signingConfig signingConfigs.debug` em:

- `apps/customer/android/app/build.gradle`
- `apps/store/android/app/build.gradle`
- `apps/driver/android/app/build.gradle`

| Campo | Valor |
|-------|--------|
| Keystore | `%USERPROFILE%\.android\debug.keystore` |
| Alias | `androiddebugkey` |
| Store/key pass | `android` |
| **SHA-1** | `4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85` |
| **SHA-256** | `D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0` |
| SHA-1 compacto | `4dd8331f75f08e642e19671254f5945370ec2a85` |

Comando para revalidar:

```bat
keytool -list -v -keystore %USERPROFILE%\.android\debug.keystore -alias androiddebugkey -storepass android -keypass android
```

## Packages a cadastrar (um app Android cada)

Instalados nos devices agora: `br.app.arrow.*` (Motorola/Honor). Não há `com.emart.*` nos aparelhos.

| App | Package (`applicationId`) | App ID local (google-services) |
|-----|---------------------------|--------------------------------|
| Customer | `br.app.arrow.customer` | `1:661081769489:android:7eea7bece5a655cfa4d3b1` |
| Store | `br.app.arrow.store` | `1:661081769489:android:7eea7bece5a655cfa4d3b2` |
| Driver | `br.app.arrow.driver` | `1:661081769489:android:7eea7bece5a655cfa4d3b3` |

**Cole em cada um destes 3 apps:**

```
SHA-1:   4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85
SHA-256: D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0
```

## Passos no Firebase Console (obrigatório — CLI sem login neste ambiente)

1. Abra: https://console.firebase.google.com/project/j-arrow/settings/general
2. Em **Your apps**, selecione cada Android app (`br.app.arrow.customer` / `store` / `driver`).
   - Se o app não existir: **Add app** → Android → package acima.
3. **Add fingerprint** → cole SHA-1 e SHA-256.
4. Authentication → Sign-in method → **Google** → Enable.
5. Aguarde 2–5 minutos (propagação OAuth).
6. Baixe `google-services.json` (deve ter `oauth_client` **não vazio**) e substitua nos 3 apps.
7. (Opcional) Copie o Web client ID para `kGoogleSignInWebClientId` em `apps/shared/lib/arrow_production_config.dart`.
8. Rebuild + reinstall e reteste “Continuar com o Google”.

## CLI (se autenticado)

```bat
firebase apps:list --project j-arrow
firebase apps:android:sha:create <APP_ID> 4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85 --project j-arrow
firebase apps:android:sha:create <APP_ID> D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0 --project j-arrow
```

Sem login CLI neste ambiente: use o console e cole os fingerprints acima.
