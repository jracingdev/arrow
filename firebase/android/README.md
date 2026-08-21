# Firebase Android — Arrow (`j-arrow`)

Os arquivos **`google-services.json` reais não vão para o Git** (`.gitignore`).

## Onde colocar cada arquivo

| App | Package Android | App ID (Console) | Destino no repo |
|-----|-----------------|------------------|-----------------|
| Cliente | `br.app.arrow.customer` | `1:661081769489:android:d8da3fce389fcabca4d3b0` | `apps/customer/android/app/google-services.json` |
| Lojista | `br.app.arrow.store` | `1:661081769489:android:c625e7c47a334c31a4d3b0` | `apps/store/android/app/google-services.json` |
| Entregador | `br.app.arrow.driver` | `1:661081769489:android:246c57cb98fff558a4d3b0` | `apps/driver/android/app/google-services.json` |

## Como gerar no Console Firebase

1. [console.firebase.google.com](https://console.firebase.google.com) → projeto **j-arrow**
2. **Project settings** → **Your apps** → **Add app** → **Android**
3. Informe o package name da tabela acima (um app Android por flavor)
4. Baixe `google-services.json` e copie para o destino indicado

## Alternativa: FlutterFire CLI

Na raiz de cada app (`apps/customer`, `apps/store`, `apps/driver`):

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
