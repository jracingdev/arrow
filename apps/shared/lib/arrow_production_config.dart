/// Production URLs and identifiers for Arrow mobile apps.
///
/// Firebase project: **j-arrow**
/// Payment/API webhooks are served by the admin Laravel panel.
const String kAdminApiBaseUrl = 'https://admin.arrow.app.br/';

/// Customer website (also loaded from Firestore `settings.websiteUrl`).
const String kWebsiteBaseUrl = 'https://arrow.app.br';

/// Store/vendor web panel (also loaded from Firestore `settings.storeUrl`).
const String kStorePanelBaseUrl = 'https://store.arrow.app.br';

/// Firebase / GCP project ID.
const String kFirebaseProjectId = 'j-arrow';

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

/// Debug keystore fingerprints used by current release builds
/// (`signingConfig signingConfigs.debug` in each app's `android/app/build.gradle`).
///
/// Source: `%USERPROFILE%\.android\debug.keystore` / alias `androiddebugkey`
/// Confirmed against `apksigner verify --print-certs` on installed APKs.
abstract final class ArrowDebugSigningSha {
  static const sha1 = '4D:D8:33:1F:75:F0:8E:64:2E:19:67:12:54:F5:94:53:70:EC:2A:85';
  static const sha256 =
      'D6:37:B2:99:45:28:39:1E:55:4D:6D:83:22:0D:33:EB:32:ED:B6:90:06:90:1D:51:18:63:5A:69:B3:8F:2C:F0';
  /// Compact form for paste into some consoles.
  static const sha1Compact = '4dd8331f75f08e642e19671254f5945370ec2a85';
}

/// Web OAuth client ID from Firebase/Google Cloud (type "Web application").
///
/// Required as `GoogleSignIn.initialize(serverClientId: …)` so Android returns
/// an `idToken` for `FirebaseAuth.signInWithCredential`.
///
/// After registering [ArrowDebugSigningSha] on each Android app in project
/// **j-arrow**, download a fresh `google-services.json` and paste the Web
/// client ID here (ends with `.apps.googleusercontent.com`).
const String kGoogleSignInWebClientId = '';
