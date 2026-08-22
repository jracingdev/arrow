/// Moeda alinhada ao painel admin/website (`currencies.decimal_degits`).
class ArrowCurrency {
  ArrowCurrency._();

  static const code = 'BRL';
  static const symbol = r'R$';
  static const decimals = 2;

  static int parseDecimals(Map<String, dynamic>? json) {
    if (json == null) return decimals;
    final raw = json['decimal_degits'] ?? json['decimal_digits'] ?? json['decimalDigits'] ?? json['decimal'];
    final n = int.tryParse('$raw');
    if (n == null || n < 0 || n > 4) return decimals;
    return n;
  }

  static String parseSymbol(Map<String, dynamic>? json) {
    final raw = (json?['symbol'] ?? '').toString().trim();
    if (raw.isEmpty || raw == r'$' || raw == r'US$' || raw.toUpperCase() == 'USD') {
      return symbol;
    }
    return raw;
  }

  static String parseCode(Map<String, dynamic>? json) {
    final raw = (json?['code'] ?? '').toString().trim().toUpperCase();
    if (raw.isEmpty || raw == 'USD') return code;
    return raw;
  }

  static bool parseSymbolAtRight(Map<String, dynamic>? json) {
    return json?['symbolAtRight'] == true || json?['symbolatright'] == true;
  }

  static String normalizeSymbol(String? symbol) {
    final raw = (symbol ?? '').trim();
    if (raw.isEmpty || raw == r'$' || raw == r'US$') return ArrowCurrency.symbol;
    return raw;
  }
}
