import 'package:arrow_shared/arrow_currency.dart';
import 'package:arrow_shared/arrow_payment_label.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:vendor/constant/constant.dart';
import 'package:vendor/models/currency_model.dart';

void main() {
  test('store Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.store,
      '1:661081769489:android:c625e7c47a334c31a4d3b0',
    );
  });

  test('moeda e rótulo de pagamento alinhados ao admin BR', () {
    final currency = CurrencyModel.fromJson({'code': 'USD', 'symbol': r'$', 'decimal_degits': 2, 'isActive': true});
    expect(currency.code, ArrowCurrency.code);
    expect(currency.symbol, ArrowCurrency.symbol);
    expect(currency.decimalDigits, 2);
    expect(Constant.paymentLabel(method: 'cod'), contains('Dinheiro'));
    expect(ArrowPaymentLabel.gateway('wallet'), 'Carteira');
    expect(ArrowPaymentLabel.gateway('mercadoPago'), 'PIX');
  });
}
