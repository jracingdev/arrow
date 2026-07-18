#!/usr/bin/env bash
# Prepara os três apps Flutter Android do monorepo Arrow para build/release.
#
# Uso (Linux/macOS ou Git Bash no Windows):
#   cd deploy && chmod +x prepare-android-apps.sh && ./prepare-android-apps.sh
#
# Opções:
#   --build-debug   Tenta flutter build apk --debug em cada app
#   --build-release Tenta flutter build appbundle em cada app (requer keystore)
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

APPS=(customer store driver)
BUILD_DEBUG=0
BUILD_RELEASE=0

for arg in "$@"; do
  case "$arg" in
    --build-debug) BUILD_DEBUG=1 ;;
    --build-release) BUILD_RELEASE=1 ;;
    -h|--help)
      echo "Uso: $0 [--build-debug] [--build-release]"
      exit 0
      ;;
  esac
done

if ! command -v flutter >/dev/null 2>&1; then
  echo "ERRO: Flutter não encontrado no PATH."
  exit 1
fi

echo "== Arrow — preparação Android =="
flutter --version
echo

check_google_services() {
  local app="$1"
  local path="$REPO_ROOT/apps/$app/android/app/google-services.json"
  if [[ -f "$path" ]]; then
    echo "  [OK] google-services.json"
  else
    echo "  [FALTA] google-services.json — veja firebase/android/README.md"
    echo "          Exemplo: firebase/android/google-services.${app}.json.example"
  fi
}

check_key_properties() {
  local app="$1"
  local path="$REPO_ROOT/apps/$app/android/key.properties"
  if [[ -f "$path" ]]; then
    echo "  [OK] key.properties (assinatura release)"
  else
    echo "  [INFO] key.properties ausente — release usa debug signing até configurar keystore"
  fi
}

for app in "${APPS[@]}"; do
  echo "---- apps/$app ----"
  APP_DIR="$REPO_ROOT/apps/$app"
  cd "$APP_DIR"

  check_google_services "$app"
  check_key_properties "$app"

  echo "  flutter pub get..."
  flutter pub get

  echo "  flutter analyze (resumo)..."
  if flutter analyze --no-fatal-infos --no-fatal-warnings 2>&1 | tail -5; then
    :
  fi

  if [[ "$BUILD_DEBUG" -eq 1 ]]; then
    echo "  flutter build apk --debug..."
    flutter build apk --debug
  fi

  if [[ "$BUILD_RELEASE" -eq 1 ]]; then
    echo "  flutter build appbundle..."
    flutter build appbundle
  fi

  echo
done

cat <<'EOF'
== Resumo manual ==

1. google-services.json (projeto j-arrow) em cada apps/*/android/app/
2. flutterfire configure --project=j-arrow (atualiza firebase_options.dart)
3. Google Maps API key em AndroidManifest (com.google.android.geo.API_KEY)
4. Keystore Play Store: android/key.properties + signingConfigs release
5. Facebook (store): strings.xml facebook_app_id / client_token reais

Comandos de build por app:
  cd apps/customer && flutter build appbundle
  cd apps/store    && flutter build appbundle
  cd apps/driver   && flutter build appbundle

Package names:
  com.emart.customer | com.emart.store | com.emart.driver

API base (GlobalURL): https://admin.arrow.app.br/
EOF
