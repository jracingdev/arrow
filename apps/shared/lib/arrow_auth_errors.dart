import 'package:firebase_auth/firebase_auth.dart';

/// Mensagens de Auth que o console Firebase pode disparar sem a Arrow usar o recurso.
class ArrowAuthErrors {
  ArrowAuthErrors._();

  static const mfaSmsNotSupported =
      'O Firebase pediu verificação extra por SMS (MFA). '
      'O login Arrow não usa MFA. No console Authentication → Método de login, '
      'desative Autenticação multifator por SMS.';

  static bool isMfaRequired(String code) {
    final normalized = code.toLowerCase().replaceFirst('auth/', '');
    return normalized == 'multi-factor-auth-required' ||
        normalized == 'second-factor-required';
  }

  static String? messageFor(Object error) {
    if (error is FirebaseAuthException && isMfaRequired(error.code)) {
      return mfaSmsNotSupported;
    }
    return null;
  }
}
