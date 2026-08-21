import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('driver package and Google Web client id are configured', () {
    expect(ArrowAndroidPackages.driver, 'br.app.arrow.driver');
    expect(kGoogleSignInWebClientId, isNotEmpty);
    expect(kGoogleSignInWebClientId, contains('.apps.googleusercontent.com'));
    expect(ArrowGoogleAuth.developerErrorToast, contains('invalid-cert-hash'));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowDebugSigningSha.sha1));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowDebugSigningSha.sha256));
    expect(ArrowGoogleAuth.reauthErrorToast, isNot(ArrowGoogleAuth.userCanceledToast));
  });
}
