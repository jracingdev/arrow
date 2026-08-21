import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('kGoogleSignInWebClientId is the j-arrow Web OAuth client', () {
    expect(kGoogleSignInWebClientId, isNotEmpty);
    expect(
      kGoogleSignInWebClientId,
      '661081769489-5e7inqhv9suqfdj4op1hms5drjtuojkd.apps.googleusercontent.com',
    );
  });

  test('Android package names match Firebase apps', () {
    expect(ArrowAndroidPackages.customer, 'br.app.arrow.customer');
    expect(ArrowAndroidPackages.store, 'br.app.arrow.store');
    expect(ArrowAndroidPackages.driver, 'br.app.arrow.driver');
  });

  test('Firebase Android app ids belong to project 661081769489', () {
    expect(
      ArrowFirebaseAndroidAppIds.customer,
      '1:661081769489:android:d8da3fce389fcabca4d3b0',
    );
    expect(
      ArrowFirebaseAndroidAppIds.store,
      '1:661081769489:android:c625e7c47a334c31a4d3b0',
    );
    expect(
      ArrowFirebaseAndroidAppIds.driver,
      '1:661081769489:android:246c57cb98fff558a4d3b0',
    );
  });

  test('local debug SHA-1 is E1:95, not the Console 4D:D8 fingerprint', () {
    expect(
      ArrowDebugSigningSha.sha1,
      'E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC',
    );
    expect(
      ArrowDebugSigningSha.sha256,
      '2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C',
    );
    expect(ArrowDebugSigningSha.sha1, isNot(ArrowFirebaseConsoleSha.sha1));
  });

  test('Google login DEVELOPER_ERROR contract includes local SHA-1', () {
    expect(ArrowGoogleAuth.developerErrorToast, contains('ApiException 10'));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowDebugSigningSha.sha1));
  });
}
