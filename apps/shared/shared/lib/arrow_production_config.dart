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

/// Android `applicationId` values — must match Play Console and `google-services.json`.
abstract final class ArrowAndroidPackages {
  static const customer = 'com.emart.customer';
  static const store = 'com.emart.store';
  static const driver = 'com.emart.driver';
}
