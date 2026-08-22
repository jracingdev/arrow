import 'package:arrow_shared/arrow_payment_label.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('rótulos de pagamento em pt-BR', () {
    expect(ArrowPaymentLabel.gateway('cod'), 'Dinheiro');
    expect(ArrowPaymentLabel.gateway('wallet'), 'Carteira');
    expect(ArrowPaymentLabel.gateway('pix'), 'PIX');
    expect(ArrowPaymentLabel.gateway('mercadoPago'), 'PIX');
    expect(ArrowPaymentLabel.gateway('Mercado Pago'), 'PIX');
    expect(ArrowPaymentLabel.gateway('stripe'), 'Cartão');
    expect(ArrowPaymentLabel.gateway('online'), 'Pagamento online');
    expect(ArrowPaymentLabel.withStatus(method: 'cod', paid: false), 'Dinheiro · A pagar');
    expect(ArrowPaymentLabel.withStatus(method: 'wallet', paid: true), 'Pago · Carteira');
    expect(ArrowPaymentLabel.isCod('Cash on Delivery'), isTrue);
  });
}
