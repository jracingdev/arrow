import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:google_sign_in/google_sign_in.dart';

void main() {
  test('kGoogleSignInWebClientId is the j-arrow Web OAuth client', () {
    expect(kGoogleSignInWebClientId, isNotEmpty);
    expect(
      kGoogleSignInWebClientId,
      '661081769489-5e7inqhv9suqfdj4op1hms5drjtuojkd.apps.googleusercontent.com',
    );
    expect(kGoogleSignInWebClientId.contains("'"), isFalse);
    expect(kGoogleSignInWebClientId.contains('"'), isFalse);
    expect(kGoogleSignInWebClientId.trim(), kGoogleSignInWebClientId);
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

  test('local debug SHA-1 is E1:95; release Arrow is 1C:CF, not Console 4D:D8', () {
    expect(
      ArrowDebugSigningSha.sha1,
      'E1:95:34:B7:ED:3D:8A:AC:5C:34:C1:CD:46:4B:1C:56:31:91:77:EC',
    );
    expect(
      ArrowDebugSigningSha.sha256,
      '2E:49:8D:5D:FB:0D:BF:69:6E:60:10:97:9F:ED:8F:B9:AB:5F:E3:CE:44:BB:CC:65:8A:8C:CC:99:32:F4:FC:7C',
    );
    expect(
      ArrowReleaseSigningSha.sha1,
      '1C:CF:2A:5A:4E:2B:CE:AE:79:06:26:BD:D5:D9:F6:2F:0C:56:9E:AD',
    );
    expect(
      ArrowReleaseSigningSha.sha256,
      '48:E1:42:7F:1F:07:B3:5F:61:58:40:50:79:60:72:03:87:74:BE:04:70:0C:35:A9:A2:02:AC:75:73:D1:7F:00',
    );
    expect(ArrowDebugSigningSha.sha1, isNot(ArrowFirebaseConsoleSha.sha1));
    expect(ArrowReleaseSigningSha.sha1, isNot(ArrowDebugSigningSha.sha1));
    expect(ArrowReleaseSigningSha.sha1, isNot(ArrowFirebaseConsoleSha.sha1));
  });

  test('Google login DEVELOPER_ERROR contract includes local SHA-1 and SHA-256', () {
    expect(ArrowGoogleAuth.developerErrorToast, contains('SHA do app nao esta no Firebase'));
    expect(ArrowGoogleAuth.developerErrorToast, contains('invalid-cert-hash'));
    expect(ArrowGoogleAuth.developerErrorToast, contains('ApiException 10'));
    expect(ArrowGoogleAuth.developerErrorToast, contains('Adicionar impressao digital'));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowDebugSigningSha.sha1));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowDebugSigningSha.sha256));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowAndroidPackages.customer));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowAndroidPackages.store));
    expect(ArrowGoogleAuth.developerErrorToast, contains(ArrowAndroidPackages.driver));
  });

  test('userMessage keeps real user cancel distinct from reauth/SHA', () {
    const realCancel = GoogleSignInException(
      code: GoogleSignInExceptionCode.canceled,
    );
    expect(ArrowGoogleAuth.userMessage(realCancel), ArrowGoogleAuth.userCanceledToast);
    expect(ArrowGoogleAuth.isDisguisedCancel(realCancel), isFalse);

    const reauth = GoogleSignInException(
      code: GoogleSignInExceptionCode.canceled,
      description: '[16] Account reauth failed.',
    );
    expect(ArrowGoogleAuth.isDisguisedCancel(reauth), isTrue);
    expect(ArrowGoogleAuth.userMessage(reauth), ArrowGoogleAuth.reauthErrorToast);
    expect(ArrowGoogleAuth.userMessage(reauth), isNot(ArrowGoogleAuth.userCanceledToast));
    expect(ArrowGoogleAuth.userMessage(reauth), contains(ArrowDebugSigningSha.sha1));
    expect(ArrowGoogleAuth.userMessage(reauth), contains(ArrowDebugSigningSha.sha256));

    const ten = GoogleSignInException(
      code: GoogleSignInExceptionCode.clientConfigurationError,
      description: 'DEVELOPER_ERROR ApiException: 10',
    );
    expect(ArrowGoogleAuth.isDisguisedCancel(ten), isFalse);
    expect(ArrowGoogleAuth.userMessage(ten), ArrowGoogleAuth.developerErrorToast);

    const signInFailed = GoogleSignInException(
      code: GoogleSignInExceptionCode.unknownError,
      description: 'ApiException: 12500',
    );
    expect(ArrowGoogleAuth.userMessage(signInFailed), ArrowGoogleAuth.developerErrorToast);

    const network = GoogleSignInException(
      code: GoogleSignInExceptionCode.unknownError,
      description: 'ApiException: 7 NETWORK_ERROR',
    );
    expect(ArrowGoogleAuth.userMessage(network), ArrowGoogleAuth.networkErrorToast);

    final firebaseCancel = FirebaseAuthException(code: 'web-context-cancelled');
    expect(ArrowGoogleAuth.userMessage(firebaseCancel), ArrowGoogleAuth.userCanceledToast);

    final firebaseUnknownTen = FirebaseAuthException(
      code: 'unknown',
      message: 'A call to GoogleApi.signIn failed with ApiException: 10',
    );
    expect(ArrowGoogleAuth.userMessage(firebaseUnknownTen), ArrowGoogleAuth.developerErrorToast);

    final firebaseNotAllowed = FirebaseAuthException(code: 'operation-not-allowed');
    expect(ArrowGoogleAuth.userMessage(firebaseNotAllowed), contains('desativado'));

    final certHash = FirebaseAuthException(code: 'invalid-cert-hash');
    expect(ArrowGoogleAuth.userMessage(certHash), ArrowGoogleAuth.developerErrorToast);
    expect(ArrowGoogleAuth.userMessage(certHash), isNot(ArrowGoogleAuth.userCanceledToast));
    expect(ArrowGoogleAuth.userMessage(certHash), contains('Adicionar impressao digital'));
    expect(ArrowGoogleAuth.userMessage(certHash), contains(ArrowDebugSigningSha.sha1));
    expect(ArrowGoogleAuth.userMessage(certHash), contains(ArrowDebugSigningSha.sha256));
  });
}
