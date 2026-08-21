import 'arrow_secure_auth_plugins.dart';

/// Namespaced Keychain/Keystore login secrets + biometric gate.
///
/// Password is never written to SharedPreferences, logs, or Firestore.
/// Google/OTP tokens are never stored. Biometrics can still lock a persisted
/// Firebase session; after sign-out those users must use Google/OTP again.
class ArrowSecureAuth {
  ArrowSecureAuth({
    required this.appId,
    required ArrowSecureStore store,
    required ArrowLocalAuthGateway localAuth,
  })  : _store = store,
        _localAuth = localAuth;

  /// Production Android applicationId / iOS bundle id, e.g. `br.app.arrow.customer`.
  final String appId;

  final ArrowSecureStore _store;
  final ArrowLocalAuthGateway _localAuth;

  static final Map<String, ArrowSecureAuth> _plugins = {};
  bool _skipNextGate = false;

  /// Real Keystore/Keychain + `local_auth`. One instance per [appId].
  factory ArrowSecureAuth.forApp(String appId) {
    return _plugins.putIfAbsent(
      appId,
      () => ArrowSecureAuth(
        appId: appId,
        store: PluginArrowSecureStore(),
        localAuth: PluginArrowLocalAuth(),
      ),
    );
  }

  static const localizedReason = 'Confirme sua identidade para entrar no Arrow';

  String get _kRememberMe => '$appId.remember_me';
  String get _kBiometrics => '$appId.biometrics';
  String get _kEmail => '$appId.email';
  String get _kPassword => '$appId.password';
  String get _kMethod => '$appId.method';
  String get _kPrompted => '$appId.prompted';

  /// Skip the next splash/login biometric gate (e.g. user tapped "Usar senha").
  void skipNextBiometricPrompt() => _skipNextGate = true;

  Future<bool> isRememberMe() async {
    final value = await _store.read(_kRememberMe);
    if (value == null) return true;
    return value == '1';
  }

  Future<bool> isBiometricsEnabled() async => (await _store.read(_kBiometrics)) == '1';

  Future<bool> hasAskedEnablePrompt() async => (await _store.read(_kPrompted)) == '1';

  Future<void> markEnablePrompted() async => _store.write(_kPrompted, '1');

  Future<ArrowLoginMethod> loginMethod() async {
    return ArrowLoginMethod.parse(await _store.read(_kMethod));
  }

  Future<String?> storedEmail() async => _store.read(_kEmail);

  Future<bool> hasPasswordSecret() async {
    final password = await _store.read(_kPassword);
    return password != null && password.isNotEmpty;
  }

  Future<ArrowStoredPassword?> passwordCredentials() async {
    final email = await _store.read(_kEmail);
    final password = await _store.read(_kPassword);
    if (email == null || email.isEmpty || password == null || password.isEmpty) {
      return null;
    }
    return ArrowStoredPassword(email: email, password: password);
  }

  /// Email to prefill on the login form (remember-me or leftover identifier).
  Future<String?> prefillEmail() async => storedEmail();

  /// Password to prefill only when Lembrar-me is on.
  Future<String?> prefillPassword() async {
    if (!await isRememberMe()) return null;
    return _store.read(_kPassword);
  }

  /// Device has at least one enrolled biometric (fingerprint / face).
  Future<bool> isBiometricAvailable() async {
    try {
      return (await _localAuth.enrolledBiometrics()).isNotEmpty;
    } catch (_) {
      return false;
    }
  }

  Future<bool> authenticate() async {
    try {
      final enrolled = await _localAuth.enrolledBiometrics();
      return await _localAuth.authenticate(
        localizedReason: localizedReason,
        biometricOnly: enrolled.isNotEmpty,
        stickyAuth: true,
      );
    } catch (_) {
      return false;
    }
  }

  Future<ArrowAuthGate> shouldAttemptLogin({required bool hasFirebaseSession}) async {
    if (_skipNextGate) {
      _skipNextGate = false;
      return ArrowAuthGate.none;
    }
    if (!await isBiometricsEnabled()) return ArrowAuthGate.none;
    if (hasFirebaseSession) return ArrowAuthGate.sessionLock;
    if (await hasPasswordSecret()) return ArrowAuthGate.credentialLogin;
    return ArrowAuthGate.none;
  }

  Future<void> setRememberMe(bool enabled) async {
    await _store.write(_kRememberMe, enabled ? '1' : '0');
    if (!enabled && !await isBiometricsEnabled()) {
      await _store.delete(_kPassword);
    }
  }

  /// Persist after a successful email/password admit.
  Future<void> savePasswordLogin({
    required String email,
    required String password,
    required bool rememberMe,
  }) async {
    final trimmedEmail = email.trim().toLowerCase();
    await _store.write(_kRememberMe, rememberMe ? '1' : '0');
    await _store.write(_kMethod, ArrowLoginMethod.password.name);
    final keepPassword = rememberMe || await isBiometricsEnabled();
    if (keepPassword) {
      await _store.write(_kEmail, trimmedEmail);
      await _store.write(_kPassword, password);
    } else {
      await _store.delete(_kEmail);
      await _store.delete(_kPassword);
    }
  }

  /// Persist after Google / Apple / phone OTP. Never stores tokens or SMS codes.
  Future<void> saveFederatedLogin({
    String? email,
    required ArrowLoginMethod method,
  }) async {
    await _store.write(_kMethod, method.name);
    await _store.delete(_kPassword);
    final identifier = email?.trim().toLowerCase();
    if (await isRememberMe() && identifier != null && identifier.contains('@')) {
      await _store.write(_kEmail, identifier);
    }
  }

  Future<ArrowBiometricToggleResult> setBiometricsEnabled(
    bool enabled, {
    String? email,
    String? password,
  }) async {
    if (enabled) {
      if (!await isBiometricAvailable()) {
        return ArrowBiometricToggleResult.unavailable;
      }
      await _store.write(_kBiometrics, '1');
      final trimmedEmail = email?.trim().toLowerCase();
      if (trimmedEmail != null && trimmedEmail.isNotEmpty) {
        await _store.write(_kEmail, trimmedEmail);
      }
      if (password != null && password.isNotEmpty) {
        await _store.write(_kPassword, password);
      }
      return ArrowBiometricToggleResult.enabled;
    }
    await _store.write(_kBiometrics, '0');
    if (!await isRememberMe()) {
      await _store.delete(_kPassword);
    }
    return ArrowBiometricToggleResult.disabled;
  }

  /// Clears Keychain/Keystore secrets and disables biometrics + remember-me.
  Future<void> forgetDevice() async {
    await _store.delete(_kEmail);
    await _store.delete(_kPassword);
    await _store.delete(_kMethod);
    await _store.write(_kBiometrics, '0');
    await _store.write(_kRememberMe, '0');
    await _store.write(_kPrompted, '0');
  }

  Future<void> clearPasswordSecret() async => _store.delete(_kPassword);

  String lockSubtitle() {
    return ArrowSecureAuthStrings.lockSubtitleFor(loginMethodSyncHint);
  }

  /// Last known method from this isolate; prefer [loginMethod] when async is possible.
  ArrowLoginMethod loginMethodSyncHint = ArrowLoginMethod.password;

  Future<String> lockSubtitleAsync() async {
    final method = await loginMethod();
    loginMethodSyncHint = method;
    return ArrowSecureAuthStrings.lockSubtitleFor(method);
  }
}

enum ArrowAuthGate {
  none,
  sessionLock,
  credentialLogin,
}

enum ArrowBiometricToggleResult { enabled, disabled, unavailable }

enum ArrowLoginMethod {
  password,
  google,
  phone,
  apple;

  static ArrowLoginMethod parse(String? raw) {
    switch (raw) {
      case 'google':
        return ArrowLoginMethod.google;
      case 'phone':
        return ArrowLoginMethod.phone;
      case 'apple':
        return ArrowLoginMethod.apple;
      default:
        return ArrowLoginMethod.password;
    }
  }

  bool get isFederated => this != ArrowLoginMethod.password;
}

class ArrowStoredPassword {
  const ArrowStoredPassword({required this.email, required this.password});
  final String email;
  final String password;
}

abstract final class ArrowSecureAuthStrings {
  static const rememberMe = 'Lembrar-me';
  static const biometricToggle = 'Entrar com biometria';
  static const enablePromptTitle = 'Usar digital ou Face ID para entrar?';
  static const enablePromptBody =
      'Na próxima vez, confirme sua identidade para abrir o Arrow sem digitar a senha.';
  static const enableYes = 'Sim';
  static const enableNo = 'Agora não';
  static const usePassword = 'Usar senha';
  static const tryAgain = 'Tentar de novo';
  static const unlockTitle = 'Desbloqueie o Arrow';
  static const forgetDevice = 'Esquecer dados neste aparelho';
  static const unavailable = 'Cadastre digital ou Face ID nas configurações do aparelho';
  static const lockPassword =
      'Use a digital ou o Face ID para entrar.';
  static const lockFederated =
      'A biometria só destrava a sessão neste aparelho. Depois de sair, entre de novo com Google ou telefone.';

  static String lockSubtitleFor(ArrowLoginMethod method) {
    return method.isFederated ? lockFederated : lockPassword;
  }
}

abstract class ArrowSecureStore {
  Future<String?> read(String key);
  Future<void> write(String key, String value);
  Future<void> delete(String key);
}

abstract class ArrowLocalAuthGateway {
  Future<List<String>> enrolledBiometrics();
  Future<bool> authenticate({
    required String localizedReason,
    required bool biometricOnly,
    required bool stickyAuth,
  });
}

class MemoryArrowSecureStore implements ArrowSecureStore {
  MemoryArrowSecureStore([Map<String, String>? seed]) : _data = {...?seed};

  final Map<String, String> _data;

  @override
  Future<String?> read(String key) async => _data[key];

  @override
  Future<void> write(String key, String value) async => _data[key] = value;

  @override
  Future<void> delete(String key) async => _data.remove(key);
}

class FakeArrowLocalAuth implements ArrowLocalAuthGateway {
  FakeArrowLocalAuth({
    this.enrolled = const ['fingerprint'],
    this.authenticateResult = true,
  });

  List<String> enrolled;
  bool authenticateResult;
  int authenticateCalls = 0;

  @override
  Future<List<String>> enrolledBiometrics() async => enrolled;

  @override
  Future<bool> authenticate({
    required String localizedReason,
    required bool biometricOnly,
    required bool stickyAuth,
  }) async {
    authenticateCalls += 1;
    return authenticateResult;
  }
}
