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
}

/// Web app used by Laravel panels (confirmed in production `__firebaseConfig`).
const String kFirebaseWebAppId = '1:661081769489:web:7eea7bece5a655cfa4d3b0';

/// Android `applicationId` / iOS bundle ID — must match Play/App Store and Firebase apps.
abstract final class ArrowAndroidPackages {
  static const customer = 'br.app.arrow.customer';
  static const store = 'br.app.arrow.store';
  static const driver = 'br.app.arrow.driver';
}

/// iOS `PRODUCT_BUNDLE_IDENTIFIER` (same values as Android applicationId).
abstract final class ArrowIosBundleIds {
  static const customer = 'br.app.arrow.customer';
  static const store = 'br.app.arrow.store';
  static const driver = 'br.app.arrow.driver';
}

/// Debug keystore fingerprints of **this machine** — what actually signs the APKs
/// (`signingConfig signingConfigs.debug` → `%USERPROFILE%\.android\debug.keystore`).
///
/// Verified 2026-08-21 with `keytool -list -v` and `apksigner verify --print-certs`.
abstract final class ArrowDebugSigningSha {
  static const sha1 = 'E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC';
  static const sha256 =
      '2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C';
  static const sha1Compact = 'e19534b7ed3d8aac5c34c1cd464b1c56319177ec';
}

/// SHA currently listed on the three Android apps in Firebase Console.
/// Does **not** match [ArrowDebugSigningSha] — Google Sign-In stays broken
/// until this local SHA-1/SHA-256 is added to each Android app in j-arrow.
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
/// After adding [ArrowDebugSigningSha] on each Android app in project
/// **j-arrow**, download a fresh `google-services.json` and paste the Web
/// client ID here (type "Web application", ends with `.apps.googleusercontent.com`).
/// Not present in production `__firebaseConfig` nor in the repo.
const String kGoogleSignInWebClientId = '';
