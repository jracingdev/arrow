# Firebase SHA fingerprints — Arrow Android (j-arrow)

Console display name: **arrow**. Project ID: **j-arrow**. Project number: **661081769489**.

Assinatura: ver `apps/README.md`. Release usa `apps/keystore/arrow-upload.jks` (não versionado).

## SHA para o Console (colar nos 3 apps)

| Origem | SHA-1 | SHA-256 |
|--------|-------|---------|
| **Debug** (USB / `flutter run`, `debug.keystore` desta máquina) | `E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC` | `2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C` |
| **Release Arrow** (`arrow-upload.jks`, alias `arrow`) | `1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD` | `48:E1:42:7F:1F:07:B3:5F:61:58:40:50:79:60:72:03:87:74:BE:04:70:0C:35:A9:A2:02:AC:75:73:D1:7F:00` |
| Já no Console (`oauth_client` type 1) — pode ficar | `4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85` | `D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0` |

**Agora (APKs debug nos aparelhos):** Adicionar impressão digital **debug** nos 3 apps. Sem isso, Google Sign-In continua `invalid-cert-hash`.

**Antes do primeiro `flutter build apk` release:** Adicionar também os SHA **release**. Não rebuildar release até isso estar no Console.

## Apps Android

| App | Package | App ID |
|-----|---------|--------|
| Customer | `br.app.arrow.customer` | `1:661081769489:android:d8da3fce389fcabca4d3b0` |
| Store | `br.app.arrow.store` | `1:661081769489:android:c625e7c47a334c31a4d3b0` |
| Driver | `br.app.arrow.driver` | `1:661081769489:android:246c57cb98fff558a4d3b0` |

Web: `1:661081769489:web:7eea7bece5a655cfa4d3b0`

## Depois de cadastrar

1. Authentication → Sign-in method → **Google** habilitado.
2. Esperar 2–5 minutos.
3. Baixar `google-services.json` atualizado se o Console tiver gerado novos `oauth_client` (arquivo gitignored).
4. Só então rebuild **release** (combinado). Debug USB já deve funcionar após o passo debug.
