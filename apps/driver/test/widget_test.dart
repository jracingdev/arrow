import 'package:arrow_shared/arrow_currency.dart';
import 'package:arrow_shared/arrow_payment_label.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/models/currency_model.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('driver Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.driver,
      '1:661081769489:android:246c57cb98fff558a4d3b0',
    );
  });

  test('moeda e rótulo de pagamento alinhados ao admin BR', () {
    final currency = CurrencyModel.fromJson({'code': 'USD', 'symbol': r'$', 'decimal_degits': 2, 'isActive': true});
    expect(currency.code, ArrowCurrency.code);
    expect(currency.symbol, ArrowCurrency.symbol);
    expect(Constant.paymentLabel(method: 'cod', paid: false), 'Dinheiro · A pagar');
    expect(ArrowPaymentLabel.gateway('online'), 'Pagamento online');
    expect(ArrowPaymentLabel.gateway('mercadoPago'), 'PIX');
    expect(ArrowPaymentLabel.isCod('Cash on Delivery'), isTrue);
  });
}
