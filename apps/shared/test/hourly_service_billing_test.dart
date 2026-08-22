import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('detects hourly price units', () {
    expect(HourlyServiceBilling.isHourly('Hourly'), isTrue);
    expect(HourlyServiceBilling.isHourly('hourly'), isTrue);
    expect(HourlyServiceBilling.isHourly('Hour'), isTrue);
    expect(HourlyServiceBilling.isHourly('por hora'), isTrue);
    expect(HourlyServiceBilling.isHourly('Fixed'), isFalse);
    expect(HourlyServiceBilling.isHourly(''), isFalse);
  });

  test('bills at least one hour and rounds to two decimals', () {
    final start = DateTime(2026, 8, 21, 10);
    expect(HourlyServiceBilling.billableHours(start, start.add(const Duration(minutes: 20))), 1);
    expect(HourlyServiceBilling.billableHours(start, start.add(const Duration(hours: 2, minutes: 30))), 2.5);
    expect(HourlyServiceBilling.billableHours(start, start), 1);
  });

  test('formats elapsed clock', () {
    expect(HourlyServiceBilling.formatElapsed(const Duration(hours: 1, minutes: 2, seconds: 3)), '01:02:03');
  });

  test('preço unitário prefere desconto e calcula total BRL', () {
    expect(HourlyServiceBilling.unitPrice('85', '0'), 85);
    expect(HourlyServiceBilling.unitPrice('85', '70'), 70);
    expect(HourlyServiceBilling.amount(85, 2), 170);
    expect(HourlyServiceBilling.amount(85, 1), 85);
  });
}
