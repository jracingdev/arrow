/// Production URLs and identifiers for Arrow mobile apps.
///
/// Firebase project: **j-arrow**
/// Payment/API webhooks are served by the admin Laravel panel.
const String kAdminApiBaseUrl = 'https://admin.arrow.app.br/';

/// Customer website (also loaded from Firestore `settings.websiteUrl`).
const String kWebsiteBaseUrl = 'https://arrow.app.br';

/// Store/vendor web panel (also loaded from Firestore `settings.storeUrl`).
const String kStorePanelBaseUrl = 'https://store.arrow.app.br';

/// Firebase / GCP project ID (`j-arrow`). Console display name is "arrow".
const String kFirebaseProjectId = 'j-arrow';

/// Android `mobilesdk_app_id` from Firebase Console (project number 661081769489).
abstract final class ArrowFirebaseAndroidAppIds {
  static const customer = '1:661081769489:android:d8da3fce389fcabca4d3b0';
  static const store = '1:661081769489:android:c625e7c47a334c31a4d3b0';
  static const driver = '1:661081769489:android:246c57cb98fff558a4d3b0';

  /// Placeholder: criar no console o app Android `br.app.arrow.provider`
  /// e substituir pelo `mobilesdk_app_id` (`1:661081769489:android:…`).
  static const provider = 'CRIAR_NO_CONSOLE';
}

/// Web app used by Laravel panels (confirmed in production `__firebaseConfig`).
const String kFirebaseWebAppId = '1:661081769489:web:7eea7bece5a655cfa4d3b0';

/// Android `applicationId` / iOS bundle ID — must match Play/App Store and Firebase apps.
abstract final class ArrowAndroidPackages {
  static const customer = 'br.app.arrow.customer';
  static const store = 'br.app.arrow.store';
  static const driver = 'br.app.arrow.driver';
  static const provider = 'br.app.arrow.provider';
}

/// iOS `PRODUCT_BUNDLE_IDENTIFIER` (same values as Android applicationId).
abstract final class ArrowIosBundleIds {
  static const customer = 'br.app.arrow.customer';
  static const store = 'br.app.arrow.store';
  static const driver = 'br.app.arrow.driver';
  static const provider = 'br.app.arrow.provider';
}

/// Coleções Firestore usadas pelo app de prestador (on-demand).
abstract final class ArrowFirestoreCollections {
  static const providerOrders = 'provider_orders';
  static const providersWorkers = 'providers_workers';
}

/// Debug keystore fingerprints of **this machine** — USB/`flutter run` builds
/// (`signingConfigs.debug` → `%USERPROFILE%\.android\debug.keystore`).
///
/// Verified 2026-08-21 with `keytool -list -v` and `apksigner verify --print-certs`.
abstract final class ArrowDebugSigningSha {
  static const sha1 = 'E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC';
  static const sha256 =
      '2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C';
  static const sha1Compact = 'e19534b7ed3d8aac5c34c1cd464b1c56319177ec';
}

/// Release keystore Arrow (`apps/keystore/arrow-upload.jks`, alias `arrow`).
/// File and passwords are gitignored. Cadastre estes SHA no Firebase **antes**
/// de `flutter build apk` (release).
abstract final class ArrowReleaseSigningSha {
  static const sha1 = '1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD';
  static const sha256 =
      '48:E1:42:7F:1F:07:B3:5F:61:58:40:50:79:60:72:03:87:74:BE:04:70:0C:35:A9:A2:02:AC:75:73:D1:7F:00';
  static const sha1Compact = '1ccf2a5a4e2bceae790626bdd5d9f62f0c569ead';
}

/// SHA leftover on the three Android apps in Firebase Console (oauth_client type 1).
/// Not an Arrow keystore on this machine. Can remain in the Console; it does not
/// sign current debug or Arrow-release APKs.
abstract final class ArrowFirebaseConsoleSha {
  static const sha1 = '4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85';
  static const sha256 =
      'D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0';
}

/// Web OAuth client ID from Firebase/Google Cloud (type "Web application").
///
/// Required as `GoogleSignIn.initialize(serverClientId: …)` so Android returns
/// an `idToken` for `FirebaseAuth.signInWithCredential`.
///
/// Type 3 (Web application) from official `google-services.json` (j-arrow).
/// Android oauth_client type 1 in that JSON is bound to
/// [ArrowFirebaseConsoleSha], not [ArrowDebugSigningSha] — Google Sign-In still
/// needs the local SHA added on each Android app in the Console.
const String kGoogleSignInWebClientId =
    '661081769489-5e7inqhv9suqfdj4op1hms5drjtuojkd.apps.googleusercontent.com';
