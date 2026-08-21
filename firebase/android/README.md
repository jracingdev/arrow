# Firebase Android — Arrow (`j-arrow`)

Os arquivos **`google-services.json` reais não vão para o Git** (`.gitignore`).

## Onde colocar cada arquivo

| App | Package Android | App ID (Console) | Destino no repo |
|-----|-----------------|------------------|-----------------|
| Cliente | `br.app.arrow.customer` | `1:661081769489:android:d8da3fce389fcabca4d3b0` | `apps/customer/android/app/google-services.json` |
| Lojista | `br.app.arrow.store` | `1:661081769489:android:c625e7c47a334c31a4d3b0` | `apps/store/android/app/google-services.json` |
| Entregador | `br.app.arrow.driver` | `1:661081769489:android:246c57cb98fff558a4d3b0` | `apps/driver/android/app/google-services.json` |
| Prestador | `br.app.arrow.provider` | `1:661081769489:android:e3046689b04067d2a4d3b0` | `apps/provider/android/app/google-services.json` — Frente 1 copia o JSON |

## App Android novo: prestador (`br.app.arrow.provider`)

App Android **já criado** no Console j-arrow (número `661081769489`). App ID: `1:661081769489:android:e3046689b04067d2a4d3b0`. Frente 1 copia o `google-services.json` para `apps/provider`. Passos (histórico / recriar SHA):

1. [console.firebase.google.com](https://console.firebase.google.com) → projeto **j-arrow** (display name **arrow**)
2. ⚙️ **Project settings** → **Your apps** → **Add app** → ícone **Android**
3. Nickname (opcional): `Arrow Provider`
4. Package name: `br.app.arrow.provider` (exato)
5. **SHA-1 debug** (colar agora — USB / `flutter run`):

```
E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC
```

6. **SHA-1 release** (colar no mesmo app; keystore `arrow-upload.jks`):

```
1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD
```

7. Register app → **Download google-services.json**
8. Frente 1 copia o JSON para `apps/provider/android/app/google-services.json`. App ID em `ArrowFirebaseAndroidAppIds.provider`: `1:661081769489:android:e3046689b04067d2a4d3b0`.
9. SHA-256 (opcional, o Console aceita SHA-1; o Google Sign-In usa SHA-1):
   - Debug: `2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C`
   - Release: `48:E1:42:7F:1F:07:B3:5F:61:58:40:50:79:60:72:03:87:74:BE:04:70:0C:35:A9:A2:02:AC:75:73:D1:7F:00`

Não fazer POST na API Firebase sem credencial. Sem APK nesta frente.

## Como gerar no Console Firebase (apps já existentes)

1. [console.firebase.google.com](https://console.firebase.google.com) → projeto **j-arrow**
2. **Project settings** → **Your apps** → **Add app** → **Android**
3. Informe o package name da tabela acima (um app Android por flavor)
4. Baixe `google-services.json` e copie para o destino indicado

## Alternativa: FlutterFire CLI

Na raiz de cada app (`apps/customer`, `apps/store`, `apps/driver`, `apps/provider`):

```bash
dart pub global activate flutterfire_cli
flutterfire configure --project=j-arrow
```

Isso atualiza `google-services.json`, `lib/firebase_options.dart` e `firebase.json`.

## Exemplos (placeholders)

Arquivos `*.example` nesta pasta mostram a estrutura esperada. **Substitua** pelos valores reais do Console.

Após copiar os JSONs, rode:

```bash
cd deploy
./prepare-android-apps.sh
```
