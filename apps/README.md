# Apps Flutter Arrow (customer / store / driver)

Pacotes Android: `br.app.arrow.customer`, `br.app.arrow.store`, `br.app.arrow.driver`.
Firebase: projeto **j-arrow**.

## Assinatura Android (release)

Os três apps usam **uma** keystore Arrow:

| Item | Valor |
|------|--------|
| Arquivo | `apps/keystore/arrow-upload.jks` (não versionado) |
| Alias | `arrow` |
| Propriedades | `apps/keystore/key.properties` (não versionado; veja `key.properties.example`) |
| Gradle | `signingConfigs.release` em `android/app/build.gradle` de cada app |

`buildTypes.release` **não** cai mais no `debug.keystore`. Build debug (`flutter run` / `flutter build apk --debug`) continua com a keystore de debug da máquina.

Não faça `flutter build apk` (release) até o SHA de **release** estar no Firebase Console — senão o login Google volta `invalid-cert-hash`.

## SHA para colar no Firebase

Console → projeto **j-arrow** → ⚙️ Configurações do projeto → seus apps → cada um dos 3 → **Adicionar impressão digital**.

### Debug (APKs USB/Wi‑Fi desta máquina)

```
SHA-1    E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC
SHA-256  2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C
```

### Release (`arrow-upload.jks`)

```
SHA-1    1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD
SHA-256  48:E1:42:7F:1F:07:B3:5F:61:58:40:50:79:60:72:03:87:74:BE:04:70:0C:35:A9:A2:02:AC:75:73:D1:7F:00
```

O SHA `4D:D8:33:1F:…` que já estiver no Console pode permanecer. Depois de colar, aguarde 2–5 min; se o JSON do Console ganhar novos `oauth_client`, baixe de novo para `android/app/google-services.json` (arquivo gitignored).

## Login Google hoje

Os APKs instalados nos aparelhos ainda são **debug** (SHA E1:95). Para o Google Sign-In funcionar **agora**, o passo humano é cadastrar o SHA **debug** nos 3 apps. Release só depois disso, com rebuild combinado.
