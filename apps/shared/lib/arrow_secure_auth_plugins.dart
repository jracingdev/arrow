import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:local_auth/local_auth.dart';

import 'arrow_secure_auth.dart';

class PluginArrowSecureStore implements ArrowSecureStore {
  PluginArrowSecureStore({FlutterSecureStorage? storage})
      : _storage = storage ??
            const FlutterSecureStorage(
              aOptions: AndroidOptions(encryptedSharedPreferences: true),
              iOptions: IOSOptions(accessibility: KeychainAccessibility.first_unlock),
            );

  final FlutterSecureStorage _storage;

  @override
  Future<String?> read(String key) async {
    try {
      return await _storage.read(key: key);
    } catch (_) {
      return null;
    }
  }

  @override
  Future<void> write(String key, String value) async {
    try {
      await _storage.write(key: key, value: value);
    } catch (_) {}
  }

  @override
  Future<void> delete(String key) async {
    try {
      await _storage.delete(key: key);
    } catch (_) {}
  }
}

class PluginArrowLocalAuth implements ArrowLocalAuthGateway {
  PluginArrowLocalAuth({LocalAuthentication? auth}) : _auth = auth ?? LocalAuthentication();

  final LocalAuthentication _auth;

  @override
  Future<List<String>> enrolledBiometrics() async {
    try {
      final types = await _auth.getAvailableBiometrics();
      return types.map((type) => type.name).toList();
    } catch (_) {
      return const [];
    }
  }

  @override
  Future<bool> authenticate({
    required String localizedReason,
    required bool biometricOnly,
    required bool stickyAuth,
  }) async {
    try {
      return await _auth.authenticate(
        localizedReason: localizedReason,
        options: AuthenticationOptions(
          stickyAuth: stickyAuth,
          biometricOnly: biometricOnly,
          useErrorDialogs: true,
        ),
      );
    } catch (_) {
      return false;
    }
  }
}
