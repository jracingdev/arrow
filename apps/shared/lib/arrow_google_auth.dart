import 'package:arrow_shared/arrow_production_config.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/foundation.dart';
import 'package:google_sign_in/google_sign_in.dart';

/// Google sign-in for Arrow Android/iOS apps.
///
/// On Android, `google_sign_in` 7.x **requires** a Web OAuth `serverClientId`.
/// When [kGoogleSignInWebClientId] is empty we use
/// [FirebaseAuth.signInWithProvider], which still needs Google enabled in
/// Firebase Auth + SHA-1 registered for the Android apps.
class ArrowGoogleAuth {
  ArrowGoogleAuth._();

  static bool _googleSignInReady = false;

  /// Mensagem padrao PT-BR com SHA-1 para colar no Firebase (nunca "unknown").
  static String get developerErrorToast =>
      'DEVELOPER_ERROR (ApiException 10). '
      'Cadastre no Firebase j-arrow o SHA-1: ${ArrowDebugSigningSha.sha1}';

  /// Returns a [UserCredential] or throws (never returns null silently).
  static Future<UserCredential> signIn() async {
    if (kGoogleSignInWebClientId.isNotEmpty) {
      return _signInWithGoogleSignInPlugin();
    }
    return _signInWithFirebaseProvider();
  }

  static Future<UserCredential> _signInWithFirebaseProvider() async {
    final provider = GoogleAuthProvider();
    provider.addScope('email');
    provider.addScope('profile');
    provider.setCustomParameters({'prompt': 'select_account'});
    try {
      return await FirebaseAuth.instance.signInWithProvider(provider);
    } on FirebaseAuthException catch (e) {
      debugPrint('ArrowGoogleAuth FirebaseAuthException code=${e.code} message=${e.message}');
      rethrow;
    }
  }

  static Future<UserCredential> _signInWithGoogleSignInPlugin() async {
    final googleSignIn = GoogleSignIn.instance;
    if (!_googleSignInReady) {
      await googleSignIn.initialize(serverClientId: kGoogleSignInWebClientId);
      _googleSignInReady = true;
    }

    final googleUser = await googleSignIn.authenticate();
    if (googleUser.id.isEmpty) {
      throw StateError('Selecao de conta Google vazia.');
    }

    final idToken = googleUser.authentication.idToken;
    if (idToken == null || idToken.isEmpty) {
      throw StateError(
        'idToken Google ausente. Cadastre SHA-1 no Firebase j-arrow: ${ArrowDebugSigningSha.sha1}',
      );
    }

    final credential = GoogleAuthProvider.credential(idToken: idToken);
    return FirebaseAuth.instance.signInWithCredential(credential);
  }

  /// Toast PT-BR — inclui SHA-1 real em erros de configuracao.
  /// Nao passar por `.tr` (traducao pode esconder o fingerprint).
  static String userMessage(Object error) {
    debugPrint('ArrowGoogleAuth error: $error');
    if (error is FirebaseAuthException) {
      final code = error.code;
      if (code == 'web-context-cancelled' || code == 'user-cancelled' || code == 'canceled') {
        return 'Login Google cancelado.';
      }
      if (code.contains('network') || code == 'network-request-failed') {
        return 'Erro de rede no login Google ($code).';
      }
      final msg = error.message ?? '';
      if (msg.contains('DEVELOPER_ERROR') ||
          msg.contains('ApiException: 10') ||
          code == 'unknown' ||
          code == 'operation-not-allowed') {
        return developerErrorToast;
      }
      return 'Falha no Google Auth: $code';
    }
    if (error is GoogleSignInException) {
      if (error.code == GoogleSignInExceptionCode.canceled) {
        return 'Login Google cancelado.';
      }
      if (error.code == GoogleSignInExceptionCode.clientConfigurationError) {
        return developerErrorToast;
      }
      return 'Falha Google Sign-In: ${error.code.name}';
    }
    final text = error.toString();
    if (text.contains('DEVELOPER_ERROR') || text.contains('ApiException: 10')) {
      return developerErrorToast;
    }
    if (text.contains('ApiException: 12500')) {
      return 'ApiException 12500 (Play Services/OAuth).';
    }
    if (text.contains('network') || text.contains('NETWORK') || text.contains('UnknownHost')) {
      return 'Sem rede para Google/Firebase.';
    }
    if (text.contains('canceled') || text.contains('cancelled') || text.contains('CANCELED')) {
      return 'Login Google cancelado.';
    }
    final short = text.length > 120 ? '${text.substring(0, 120)}…' : text;
    return 'Falha no Google: $short';
  }
}
