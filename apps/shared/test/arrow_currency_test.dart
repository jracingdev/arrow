import 'package:arrow_shared/arrow_currency.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('lê decimal_degits do admin e rejeita USD residual', () {
    expect(ArrowCurrency.parseDecimals({'decimal_degits': 2}), 2);
    expect(ArrowCurrency.parseDecimals({'decimalDigits': 2}), 2);
    expect(ArrowCurrency.parseDecimals({}), 2);
    expect(ArrowCurrency.parseSymbol({'symbol': r'$'}), r'R$');
    expect(ArrowCurrency.parseSymbol({'symbol': r'R$'}), r'R$');
    expect(ArrowCurrency.parseCode({'code': 'USD'}), 'BRL');
    expect(ArrowCurrency.normalizeSymbol(''), r'R$');
    expect(ArrowCurrency.normalizeSymbol(r'$'), r'R$');
  });
}
