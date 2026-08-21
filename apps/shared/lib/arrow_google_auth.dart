import 'package:arrow_shared/arrow_auth_errors.dart';
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

  static String get _webClientId => kGoogleSignInWebClientId.trim();

  /// Mensagem padrao PT-BR com fingerprints para colar no Firebase (nunca "unknown").
  static String get developerErrorToast =>
      'SHA do app nao esta no Firebase (invalid-cert-hash / ApiException 10). '
      'Console j-arrow → cada app (${ArrowAndroidPackages.customer}, '
      '${ArrowAndroidPackages.store}, ${ArrowAndroidPackages.driver}) → '
      'Adicionar impressao digital. '
      'SHA-1 deste APK: ${ArrowDebugSigningSha.sha1} ; '
      'SHA-256: ${ArrowDebugSigningSha.sha256}.';

  /// GMS devolveu canceled com reauth / status 16 — nao e back-button do usuario.
  static String get reauthErrorToast =>
      'Google recusou o idToken apos escolher a conta '
      '(codigo 16 / Account reauth failed). '
      'Reconecte a conta em Configuracoes → Google neste aparelho. '
      'Se persistir, cadastre no Firebase j-arrow (customer, store e driver) '
      'SHA-1 ${ArrowDebugSigningSha.sha1} e SHA-256 ${ArrowDebugSigningSha.sha256}';

  static const String userCanceledToast = 'Login Google cancelado.';

  static const String networkErrorToast = 'Erro de rede no login Google.';

  /// Returns a [UserCredential] or throws (never returns null silently).
  static Future<UserCredential> signIn() async {
    if (_webClientId.isNotEmpty) {
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
      debugPrint(
        'ArrowGoogleAuth FirebaseAuthException code=${e.code} message=${e.message}',
      );
      rethrow;
    }
  }

  static Future<UserCredential> _signInWithGoogleSignInPlugin() async {
    final googleSignIn = GoogleSignIn.instance;
    if (!_googleSignInReady) {
      await googleSignIn.initialize(serverClientId: _webClientId);
      _googleSignInReady = true;
    }

    // Plugin docs: do not call authenticate() until after signOut().
    try {
      await googleSignIn.signOut();
    } catch (e) {
      debugPrint('ArrowGoogleAuth signOut ignored: $e');
    }

    GoogleSignInAccount googleUser;
    try {
      googleUser = await googleSignIn.authenticate();
    } on GoogleSignInException catch (e) {
      debugPrint(
        'ArrowGoogleAuth GoogleSignInException '
        'code=${e.code.name} description=${e.description} details=${e.details}',
      );
      if (isDisguisedCancel(e)) {
        debugPrint(
          'ArrowGoogleAuth native flow failed with disguised cancel; '
          'falling back to FirebaseAuth.signInWithProvider',
        );
        return _signInWithFirebaseProvider();
      }
      rethrow;
    }

    if (googleUser.id.isEmpty) {
      throw StateError('Selecao de conta Google vazia.');
    }

    final idToken = googleUser.authentication.idToken;
    if (idToken == null || idToken.isEmpty) {
      throw StateError(
        'idToken Google ausente. Cadastre SHA-1 no Firebase j-arrow: '
        '${ArrowDebugSigningSha.sha1}',
      );
    }

    final credential = GoogleAuthProvider.credential(idToken: idToken);
    return FirebaseAuth.instance.signInWithCredential(credential);
  }

  /// True when Play Services maps SHA / sessao morta para
  /// [GoogleSignInExceptionCode.canceled] instead of DEVELOPER_ERROR.
  static bool isDisguisedCancel(GoogleSignInException error) {
    if (error.code != GoogleSignInExceptionCode.canceled) {
      return false;
    }
    return looksLikeDeveloperOrReauth(_errorBlob(error));
  }

  static bool looksLikeCertHash(String blob) {
    return blob.contains('invalid-cert-hash') ||
        blob.contains('invalid_cert_hash') ||
        blob.contains('cert-hash') ||
        blob.contains('certificate_hash');
  }

  static bool looksLikeDeveloperOrReauth(String blob) {
    if (looksLikeCertHash(blob) ||
        blob.contains('reauth') ||
        blob.contains('account reauth failed') ||
        blob.contains('[16]') ||
        blob.contains('status code 16') ||
        blob.contains('commonstatuscodes.canceled') ||
        blob.contains('developer_error') ||
        blob.contains('apiexception: 10') ||
        blob.contains('apiexception 10') ||
        blob.contains('statuscode=10') ||
        blob.contains('12500') ||
        blob.contains('sha-1') ||
        blob.contains('current app sha')) {
      return true;
    }
    // Bare "canceled" / 12501 without extra context is a real user dismiss.
    return false;
  }

  static bool looksLikeNetwork(String blob) {
    return blob.contains('network') ||
        blob.contains('unknownhost') ||
        blob.contains('apiexception: 7') ||
        blob.contains('statuscode=7');
  }

  static String _errorBlob(Object error) {
    if (error is GoogleSignInException) {
      return '${error.code.name} ${error.description ?? ''} ${error.details ?? ''} ${error.toString()}'
          .toLowerCase();
    }
    if (error is FirebaseAuthException) {
      return '${error.code} ${error.message ?? ''} ${error.toString()}'.toLowerCase();
    }
    return error.toString().toLowerCase();
  }

  /// Toast PT-BR — inclui SHA real em erros de configuracao.
  /// Nao passar por `.tr` (traducao pode esconder o fingerprint).
  static String userMessage(Object error) {
    debugPrint('ArrowGoogleAuth error: $error');
    final blob = _errorBlob(error);

    if (looksLikeNetwork(blob)) {
      if (error is FirebaseAuthException) {
        return 'Erro de rede no login Google (${error.code}).';
      }
      return networkErrorToast;
    }

    if (looksLikeDeveloperOrReauth(blob)) {
      if (looksLikeCertHash(blob) ||
          blob.contains('developer_error') ||
          blob.contains('apiexception: 10') ||
          blob.contains('apiexception 10')) {
        return developerErrorToast;
      }
      if (blob.contains('reauth') || blob.contains('[16]')) {
        return reauthErrorToast;
      }
      return developerErrorToast;
    }

    if (error is FirebaseAuthException) {
      final mfa = ArrowAuthErrors.messageFor(error);
      if (mfa != null) {
        return mfa;
      }
      final code = error.code;
      if (code == 'invalid-cert-hash' || code.contains('cert-hash')) {
        return developerErrorToast;
      }
      if (code == 'web-context-cancelled' ||
          code == 'user-cancelled' ||
          code == 'canceled') {
        return userCanceledToast;
      }
      if (code == 'operation-not-allowed') {
        return 'Provedor Google desativado no Firebase Auth (j-arrow).';
      }
      return 'Falha no Google Auth: $code';
    }

    if (error is GoogleSignInException) {
      switch (error.code) {
        case GoogleSignInExceptionCode.canceled:
          return userCanceledToast;
        case GoogleSignInExceptionCode.clientConfigurationError:
        case GoogleSignInExceptionCode.providerConfigurationError:
          return developerErrorToast;
        default:
          return 'Falha Google Sign-In: ${error.code.name}'
              '${error.description == null ? '' : ' (${error.description})'}';
      }
    }

    if (blob.contains('canceled') || blob.contains('cancelled')) {
      return userCanceledToast;
    }
    if (blob.contains('12501')) {
      return userCanceledToast;
    }

    final text = error.toString();
    final short = text.length > 120 ? '${text.substring(0, 120)}…' : text;
    return 'Falha no Google: $short';
  }
}
