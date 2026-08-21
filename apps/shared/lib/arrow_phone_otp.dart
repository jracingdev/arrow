import 'dart:convert';
import 'dart:math';

import 'package:arrow_shared/arrow_auth_errors.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:http/http.dart' as http;

/// Phone login OTP delivered on-device (FCM + in-app), not SMS.
///
/// Firebase Phone Auth SMS is not used: it needs Blaze billing and often
/// fails to arrive in Brazil. The admin API mints a Firebase custom token
/// after the user confirms the code shown/notified on this device.
class ArrowPhoneOtp {
  ArrowPhoneOtp._();

  static const sendPath = 'api/otp/send';
  static const verifyPath = 'api/otp/verify';

  static Uri get sendUri => Uri.parse('$kAdminApiBaseUrl$sendPath');
  static Uri get verifyUri => Uri.parse('$kAdminApiBaseUrl$verifyPath');

  static String newSessionId() {
    final rnd = Random.secure();
    final bytes = List<int>.generate(16, (_) => rnd.nextInt(256));
    bytes[6] = (bytes[6] & 0x0f) | 0x40;
    bytes[8] = (bytes[8] & 0x3f) | 0x80;
    final hex = bytes.map((b) => b.toRadixString(16).padLeft(2, '0')).join();
    return '${hex.substring(0, 8)}-${hex.substring(8, 12)}-${hex.substring(12, 16)}-${hex.substring(16, 20)}-${hex.substring(20)}';
  }

  static Future<ArrowOtpSendResult> send({
    required String e164,
    required String sessionId,
    String? fcmToken,
  }) async {
    final response = await http.post(
      sendUri,
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({
        'phone': e164,
        'sessionId': sessionId,
        'fcmToken': fcmToken ?? '',
      }),
    );
    final json = _decode(response.body);
    if (response.statusCode >= 200 && response.statusCode < 300 && json['success'] == true) {
      return ArrowOtpSendResult(
        sessionId: json['sessionId'] as String? ?? sessionId,
        displayCode: json['displayCode'] as String? ?? '',
        fcmDelivered: json['fcmDelivered'] == true,
      );
    }
    throw ArrowOtpException(json['message'] as String? ?? 'Falha ao enviar o código.');
  }

  static Future<UserCredential> signInWithCode({
    required String e164,
    required String sessionId,
    required String code,
  }) async {
    final response = await http.post(
      verifyUri,
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({
        'phone': e164,
        'sessionId': sessionId,
        'code': code,
      }),
    );
    final json = _decode(response.body);
    if (response.statusCode < 200 || response.statusCode >= 300 || json['success'] != true) {
      throw ArrowOtpException(json['message'] as String? ?? 'Código inválido.');
    }

    try {
      final token = json['customToken'] as String? ?? '';
      if (token.isNotEmpty) {
        return await FirebaseAuth.instance.signInWithCustomToken(token);
      }

      final email = json['email'] as String? ?? '';
      final password = json['password'] as String? ?? '';
      if (email.isNotEmpty && password.isNotEmpty) {
        return await FirebaseAuth.instance.signInWithEmailAndPassword(email: email, password: password);
      }
    } on FirebaseAuthException catch (e) {
      final mfa = ArrowAuthErrors.messageFor(e);
      if (mfa != null) {
        throw ArrowOtpException(mfa);
      }
      rethrow;
    }

    throw ArrowOtpException('Sessão Firebase inválida.');
  }

  static Map<String, dynamic> _decode(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map<String, dynamic>) return decoded;
      if (decoded is Map) return Map<String, dynamic>.from(decoded);
    } catch (_) {}
    return {};
  }
}

class ArrowOtpSendResult {
  const ArrowOtpSendResult({
    required this.sessionId,
    required this.displayCode,
    required this.fcmDelivered,
  });

  final String sessionId;
  final String displayCode;
  final bool fcmDelivered;
}

class ArrowOtpException implements Exception {
  ArrowOtpException(this.message);
  final String message;

  @override
  String toString() => message;
}
