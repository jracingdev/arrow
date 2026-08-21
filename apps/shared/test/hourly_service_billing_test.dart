import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('detects hourly price units', () {
    expect(HourlyServiceBilling.isHourly('Hourly'), isTrue);
    expect(HourlyServiceBilling.isHourly('hourly'), isTrue);
    expect(HourlyServiceBilling.isHourly('Fixed'), isFalse);
  });

  test('bills at least one hour and rounds to two decimals', () {
    final start = DateTime(2026, 8, 21, 10);
    expect(HourlyServiceBilling.billableHours(start, start.add(const Duration(minutes: 20))), 1);
    expect(HourlyServiceBilling.billableHours(start, start.add(const Duration(hours: 2, minutes: 30))), 2.5);
  });

  test('formats elapsed clock', () {
    expect(HourlyServiceBilling.formatElapsed(const Duration(hours: 1, minutes: 2, seconds: 3)), '01:02:03');
  });
}
