import 'package:arrow_shared/arrow_auth_errors.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('MFA SMS is detected and mapped to a console instruction', () {
    expect(ArrowAuthErrors.isMfaRequired('multi-factor-auth-required'), isTrue);
    expect(ArrowAuthErrors.isMfaRequired('auth/multi-factor-auth-required'), isTrue);
    expect(ArrowAuthErrors.isMfaRequired('second-factor-required'), isTrue);
    expect(ArrowAuthErrors.isMfaRequired('wrong-password'), isFalse);

    final error = FirebaseAuthException(code: 'multi-factor-auth-required');
    expect(ArrowAuthErrors.messageFor(error), ArrowAuthErrors.mfaSmsNotSupported);
    expect(ArrowAuthErrors.messageFor(error), contains('desative Autenticação multifator por SMS'));
    expect(ArrowAuthErrors.messageFor(FirebaseAuthException(code: 'wrong-password')), isNull);
  });
}
