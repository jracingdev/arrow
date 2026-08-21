import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  late MemoryArrowSecureStore store;
  late FakeArrowLocalAuth localAuth;
  late ArrowSecureAuth auth;

  setUp(() {
    store = MemoryArrowSecureStore();
    localAuth = FakeArrowLocalAuth();
    auth = ArrowSecureAuth(
      appId: 'br.app.arrow.customer',
      store: store,
      localAuth: localAuth,
    );
  });

  test('remember-me defaults on and persists email+password', () async {
    expect(await auth.isRememberMe(), isTrue);
    await auth.savePasswordLogin(
      email: 'User@Arrow.app.br',
      password: 's3cret',
      rememberMe: true,
    );
    expect(await auth.isRememberMe(), isTrue);
    expect(await auth.prefillEmail(), 'user@arrow.app.br');
    expect(await auth.prefillPassword(), 's3cret');
    expect(await auth.hasPasswordSecret(), isTrue);
    expect(await auth.loginMethod(), ArrowLoginMethod.password);
  });

  test('remember-me off stores nothing unless biometrics is on', () async {
    await auth.savePasswordLogin(email: 'a@b.com', password: 'x', rememberMe: false);
    expect(await auth.prefillEmail(), isNull);
    expect(await auth.prefillPassword(), isNull);
    expect(await auth.hasPasswordSecret(), isFalse);

    await auth.setBiometricsEnabled(true, email: 'a@b.com', password: 'x');
    await auth.savePasswordLogin(email: 'a@b.com', password: 'x', rememberMe: false);
    expect(await auth.hasPasswordSecret(), isTrue);
    expect(await auth.prefillPassword(), isNull);
  });

  test('biometrics off keeps password when remember-me is on', () async {
    await auth.savePasswordLogin(email: 'a@b.com', password: 'keep', rememberMe: true);
    await auth.setBiometricsEnabled(true);
    await auth.setBiometricsEnabled(false);
    expect(await auth.isBiometricsEnabled(), isFalse);
    expect(await auth.hasPasswordSecret(), isTrue);
  });

  test('biometrics off clears password when remember-me is off', () async {
    await auth.setBiometricsEnabled(true, email: 'a@b.com', password: 'gone');
    await auth.setRememberMe(false);
    await auth.setBiometricsEnabled(false);
    expect(await auth.hasPasswordSecret(), isFalse);
  });

  test('shouldAttemptLogin: session lock vs credential login vs none', () async {
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.none);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: false), ArrowAuthGate.none);

    await auth.setBiometricsEnabled(true);
    expect(await auth.isBiometricsEnabled(), isTrue);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.sessionLock);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: false), ArrowAuthGate.none);

    await auth.savePasswordLogin(email: 'a@b.com', password: 'pw', rememberMe: true);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: false), ArrowAuthGate.credentialLogin);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.sessionLock);
  });

  test('Google/OTP never stores a password and skips credential login', () async {
    await auth.setRememberMe(true);
    await auth.setBiometricsEnabled(true);
    await auth.saveFederatedLogin(email: 'google@arrow.app.br', method: ArrowLoginMethod.google);
    expect(await auth.hasPasswordSecret(), isFalse);
    expect(await auth.prefillEmail(), 'google@arrow.app.br');
    expect(await auth.loginMethod(), ArrowLoginMethod.google);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: false), ArrowAuthGate.none);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.sessionLock);
    expect(await auth.lockSubtitleAsync(), contains('Google ou telefone'));
  });

  test('forgetDevice clears secrets and disables biometrics', () async {
    await auth.savePasswordLogin(email: 'a@b.com', password: 'pw', rememberMe: true);
    await auth.setBiometricsEnabled(true);
    await auth.forgetDevice();
    expect(await auth.isBiometricsEnabled(), isFalse);
    expect(await auth.isRememberMe(), isFalse);
    expect(await auth.hasPasswordSecret(), isFalse);
    expect(await auth.prefillEmail(), isNull);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.none);
  });

  test('skipNextBiometricPrompt bypasses one gate', () async {
    await auth.setBiometricsEnabled(true);
    auth.skipNextBiometricPrompt();
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.none);
    expect(await auth.shouldAttemptLogin(hasFirebaseSession: true), ArrowAuthGate.sessionLock);
  });

  test('unavailable hardware does not enable biometrics', () async {
    localAuth.enrolled = const [];
    final result = await auth.setBiometricsEnabled(true);
    expect(result, ArrowBiometricToggleResult.unavailable);
    expect(await auth.isBiometricsEnabled(), isFalse);
  });

  test('app namespaces do not clash', () async {
    final driver = ArrowSecureAuth(
      appId: 'br.app.arrow.driver',
      store: store,
      localAuth: localAuth,
    );
    await auth.savePasswordLogin(email: 'c@arrow.app.br', password: 'customer', rememberMe: true);
    await driver.savePasswordLogin(email: 'd@arrow.app.br', password: 'driver', rememberMe: true);
    expect(await auth.prefillEmail(), 'c@arrow.app.br');
    expect(await driver.prefillEmail(), 'd@arrow.app.br');
    expect(await auth.prefillPassword(), 'customer');
    expect(await driver.prefillPassword(), 'driver');
  });
}
