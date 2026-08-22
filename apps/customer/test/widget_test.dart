import 'package:arrow_shared/arrow_currency.dart';
import 'package:arrow_shared/arrow_i18n.dart';
import 'package:arrow_shared/arrow_payment_label.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/published_service_visibility.dart';
import 'package:customer/models/currency_model.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('customer Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.customer,
      '1:661081769489:android:d8da3fce389fcabca4d3b0',
    );
  });

  test('serviço publicado sem plano aparece e moeda vem do admin', () {
    expect(
      PublishedServiceVisibility.passesListing(
        subscriptionModelApplied: true,
        hasPlan: false,
        expired: false,
      ),
      isTrue,
    );
    final currency = CurrencyModel.fromJson({'code': 'USD', 'symbol': r'$', 'decimal_degits': 2, 'isActive': true});
    expect(currency.code, ArrowCurrency.code);
    expect(currency.symbol, ArrowCurrency.symbol);
    expect(currency.decimal, 2);
    expect(ArrowPaymentLabel.gateway('mercadoPago'), 'PIX');
    expect(ArrowI18n.dayLabel('Monday'), 'Segunda-feira');
    expect(ArrowCurrency.normalizeSymbol(r'$'), r'R$');
  });
}
