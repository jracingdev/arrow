import 'package:firebase_auth/firebase_auth.dart';

/// Helpers for Firebase Phone Auth error UX (PT-BR).
class ArrowPhoneAuth {
  ArrowPhoneAuth._();

  static const phoneProviderDisabledToast =
      'Login por telefone desativado no Firebase. '
      'Ative Phone em Authentication → Sign-in method '
      '(projeto j-arrow).';

  static const invalidPhoneToast = 'Número de telefone inválido.';

  static const tooManyRequestsToast =
      'Muitas tentativas. Aguarde um momento e tente de novo.';

  /// Maps [FirebaseAuthException] from [verifyPhoneNumber] to a short PT-BR toast.
  static String toastFor(FirebaseAuthException e) {
    switch (e.code) {
      case 'operation-not-allowed':
        return phoneProviderDisabledToast;
      case 'invalid-phone-number':
        return invalidPhoneToast;
      case 'too-many-requests':
        return tooManyRequestsToast;
      default:
        final msg = e.message ?? '';
        if (msg.contains('sign-in provider is disabled') ||
            msg.contains('This operation is not allowed')) {
          return phoneProviderDisabledToast;
        }
        return msg.isNotEmpty ? msg : 'Falha no login por telefone (${e.code}).';
    }
  }

  /// Maps unknown/catch errors that may wrap an [FirebaseAuthException].
  static String toastForError(Object error) {
    if (error is FirebaseAuthException) return toastFor(error);
    final text = error.toString();
    if (text.contains('operation-not-allowed') ||
        text.contains('sign-in provider is disabled') ||
        text.contains('This operation is not allowed')) {
      return phoneProviderDisabledToast;
    }
    if (text.contains('too-many-requests')) return tooManyRequestsToast;
    return tooManyRequestsToast;
  }
}
