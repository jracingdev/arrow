/// Rótulos de pagamento em pt-BR (valores persistidos no Firestore ficam em inglês).
class ArrowPaymentLabel {
  ArrowPaymentLabel._();

  static bool isCod(String? method) {
    final m = (method ?? '').trim().toLowerCase();
    return m == 'cod' || m == 'cash on delivery' || m == 'dinheiro' || m == 'dinheiro na entrega';
  }

  static String gateway(String? method) {
    switch ((method ?? '').trim().toLowerCase()) {
      case 'cod':
      case 'cash on delivery':
      case 'dinheiro':
      case 'dinheiro na entrega':
        return 'Dinheiro';
      case 'wallet':
        return 'Carteira';
      case 'pix':
      case 'mercadopago':
      case 'mercado pago':
        return 'PIX';
      case 'stripe':
        return 'Cartão';
      case 'paypal':
        return 'PayPal';
      case 'online':
        return 'Pagamento online';
      case 'tax':
        return 'Taxa';
      case 'cashback amount':
        return 'Cashback';
      case 'referral amount':
        return 'Indicação';
      default:
        final raw = (method ?? '').trim();
        return raw.isEmpty ? '—' : raw;
    }
  }

  static String withStatus({required String? method, bool? paid}) {
    final label = gateway(method);
    if (isCod(method)) {
      return paid == true ? 'Dinheiro · Pago' : 'Dinheiro · A pagar';
    }
    if (paid == true) return 'Pago · $label';
    if (paid == false) return 'A pagar · $label';
    return label;
  }
}
