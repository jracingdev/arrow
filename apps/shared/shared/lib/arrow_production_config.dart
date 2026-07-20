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
