# Keystore de release Arrow

Arquivo único dos 3 apps Android (`customer`, `store`, `driver`).

- **Path:** `apps/keystore/arrow-upload.jks`
- **Alias:** `arrow`
- **Validade:** 10000 dias
- **DN:** CN=Arrow, OU=Mobile, O=Arrow, L=Brasil, ST=BR, C=BR

## Segredos (não vão para o git)

Copie `key.properties.example` para `key.properties` nesta pasta e preencha as senhas.
O Gradle dos 3 apps lê `apps/keystore/key.properties`.

Nunca commite:

- `arrow-upload.jks`
- `key.properties`

Backup o `.jks` e o `key.properties` fora do repositório (Play App Signing / cofre da equipe). Sem esse arquivo não dá para atualizar o app na Play Store.

## Fingerprints (Firebase Console)

Cole **debug e release** em cada app Android (customer, store, driver). O SHA `4D:D8:…` que já estiver no Console pode ficar.

Ver `apps/README.md`.
