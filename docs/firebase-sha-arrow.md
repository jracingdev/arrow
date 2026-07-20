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

| App | Package (`applicationId`) |
|-----|---------------------------|
| Customer | `br.app.arrow.customer` |
| Store | `br.app.arrow.store` |
| Driver | `br.app.arrow.driver` |

## Passos no Firebase Console

1. Abra [Firebase Console](https://console.firebase.google.com/) → projeto **j-arrow**.
2. ⚙️ Project settings → **Your apps**.
3. Para cada package acima (adicione o app Android se ainda não existir):
   - **Add fingerprint**
   - Cole o **SHA-1** e o **SHA-256** desta página.
4. Authentication → Sign-in method → **Google** → Enable (se ainda não estiver).
5. Baixe o novo `google-services.json` de **cada** app (deve conter `oauth_client` **não vazio**) e substitua:
   - `apps/customer/android/app/google-services.json`
   - `apps/store/android/app/google-services.json`
   - `apps/driver/android/app/google-services.json`
6. Em Google Cloud → APIs & Services → Credentials, copie o client ID tipo **Web application** (`….apps.googleusercontent.com`) para:
   - `apps/shared/lib/arrow_production_config.dart` → `kGoogleSignInWebClientId`
7. Rebuild release + reinstall nos devices.

## CLI (se autenticado)

```bat
firebase apps:list --project j-arrow
firebase apps:android:sha:create <APP_ID> 4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85 --project j-arrow
firebase apps:android:sha:create <APP_ID> D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0 --project j-arrow
```

Sem login CLI neste ambiente: use o console e cole os fingerprints acima.
