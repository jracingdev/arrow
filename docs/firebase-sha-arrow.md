# Firebase SHA fingerprints — Arrow Android (j-arrow)

Console display name: **arrow**. Project ID: **j-arrow**. Project number: **661081769489**.

## SHA: local keystore vs Firebase Console

Os APKs desta máquina **não** usam o SHA que está no Console.

| Origem | SHA-1 | SHA-256 |
|--------|-------|---------|
| **Keystore local** (`%USERPROFILE%\.android\debug.keystore`, alias `androiddebugkey`) e APKs `app-release.apk` | `E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC` | `2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C` |
| **Firebase Console** (cadastrado nos 3 apps Android) | `4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85` | `D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0` |

**Ação obrigatória no Console:** em cada app Android, **Add fingerprint** com o SHA **local** (E1:95:… / 2E:49:…). Sem isso, Google Sign-In continua `DEVELOPER_ERROR` / `ApiException: 10`.

O SHA `4D:D8:…` pode ficar cadastrado (não atrapalha), mas **não** assina os APKs atuais. Não há outra keystore no repo.

```bat
keytool -list -v -keystore %USERPROFILE%\.android\debug.keystore -alias androiddebugkey -storepass android -keypass android
```

## Apps Android reais (Firebase Console)

| App | Package | App ID |
|-----|---------|--------|
| Customer | `br.app.arrow.customer` | `1:661081769489:android:d8da3fce389fcabca4d3b0` |
| Store | `br.app.arrow.store` | `1:661081769489:android:c625e7c47a334c31a4d3b0` |
| Driver | `br.app.arrow.driver` | `1:661081769489:android:246c57cb98fff558a4d3b0` |

Web (painéis Laravel, `__firebaseConfig` em produção): `1:661081769489:web:7eea7bece5a655cfa4d3b0`

IDs `…:android:7eea7bece5a655cfa4d3b1/b2/b3` eram placeholders locais — **não** existem no Console.

## Depois de cadastrar o SHA local

1. Authentication → Sign-in method → **Google** → Enable.
2. Aguarde 2–5 minutos.
3. Baixe `google-services.json` de cada app (deve ter `oauth_client` **não vazio**) e coloque em `apps/<app>/android/app/` (não commitado).
4. Copie o client ID tipo **Web application** (`*.apps.googleusercontent.com`) para `kGoogleSignInWebClientId` em `apps/shared/lib/arrow_production_config.dart`.
5. Rebuild + reinstall.
