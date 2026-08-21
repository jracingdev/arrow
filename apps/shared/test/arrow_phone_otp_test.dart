import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('OTP endpoints live on the admin API', () {
    expect(ArrowPhoneOtp.sendUri.toString(), 'https://admin.arrow.app.br/api/otp/send');
    expect(ArrowPhoneOtp.verifyUri.toString(), 'https://admin.arrow.app.br/api/otp/verify');
    expect(kAdminApiBaseUrl.endsWith('/'), isTrue);
  });

  test('newSessionId is a UUID-shaped token', () {
    final id = ArrowPhoneOtp.newSessionId();
    expect(id.contains(' '), isFalse);
    expect(RegExp(r'^[0-9a-f-]{36}$').hasMatch(id), isTrue);
    expect(ArrowPhoneOtp.newSessionId(), isNot(id));
  });

  test('toE164 builds Brazilian numbers', () {
    expect(BrazilPhone.toE164('11987654321'), '+5511987654321');
    expect(BrazilPhone.toE164('(11) 98765-4321', '+55'), '+5511987654321');
  });
}
