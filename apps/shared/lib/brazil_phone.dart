import 'package:flutter/services.dart';

/// Telefone brasileiro (sem DDI):
/// - Celular: (XX) 9XXXX-XXXX — 11 dígitos
/// - Fixo:    (XX) XXXX-XXXX  — 10 dígitos
class BrazilPhone {
  BrazilPhone._();

  static const String dialCode = '+55';
  static const String isoCode = 'BR';
  static const String hint = '(00) 00000-0000';

  /// DDDs válidos (códigos de área ANATEL).
  static const Set<String> validDdds = {
    '11', '12', '13', '14', '15', '16', '17', '18', '19',
    '21', '22', '24', '27', '28',
    '31', '32', '33', '34', '35', '37', '38',
    '41', '42', '43', '44', '45', '46', '47', '48', '49',
    '51', '53', '54', '55',
    '61', '62', '63', '64', '65', '66', '67', '68', '69',
    '71', '73', '74', '75', '77', '79',
    '81', '82', '83', '84', '85', '86', '87', '88', '89',
    '91', '92', '93', '94', '95', '96', '97', '98', '99',
  };

  static String digitsOnly(String? value) => (value ?? '').replaceAll(RegExp(r'\D'), '');

  /// Formata dígitos nacionais para máscara BR.
  static String format(String? value) {
    final d = digitsOnly(value);
    if (d.isEmpty) return '';
    if (d.length <= 2) return '($d';
    if (d.length <= 6) return '(${d.substring(0, 2)}) ${d.substring(2)}';
    if (d.length <= 10) {
      return '(${d.substring(0, 2)}) ${d.substring(2, 6)}-${d.substring(6)}';
    }
    final clipped = d.length > 11 ? d.substring(0, 11) : d;
    return '(${clipped.substring(0, 2)}) ${clipped.substring(2, 7)}-${clipped.substring(7)}';
  }

  /// Valida número nacional BR (10 fixo ou 11 celular com 9 após DDD).
  static bool isValid(String? value) {
    final d = digitsOnly(value);
    if (d.length != 10 && d.length != 11) return false;
    final ddd = d.substring(0, 2);
    if (!validDdds.contains(ddd)) return false;
    if (d.length == 11 && d[2] != '9') return false;
    return true;
  }

  /// Valida considerando o DDI selecionado (+55 → regras BR; outros → 8–15 dígitos).
  static bool isValidForDialCode(String? value, String? dial) {
    final normalized = normalizeDialCode(dial);
    if (normalized == dialCode || normalized == '55') {
      return isValid(value);
    }
    final d = digitsOnly(value);
    return d.length >= 8 && d.length <= 15;
  }

  static String normalizeDialCode(String? raw) {
    final v = (raw ?? '').trim();
    // Produto Arrow é BR-only: qualquer valor inválido/vazio → +55.
    // Evita DDI errado vindo do painel (ex.: +850) ou string vazia no picker.
    if (v.isEmpty) return dialCode;
    if (v.toUpperCase() == 'BR' || v == dialCode || v == '55' || v == '+55') {
      return dialCode;
    }
    if (v.startsWith('+') && v == dialCode) return dialCode;
    // Ignora outros DDIs/códigos (ex. +850, KP, IN) e mantém Brasil.
    return dialCode;
  }

  static String normalizeIsoCode(String? raw) {
    final v = (raw ?? '').trim();
    if (v.isEmpty) return isoCode;
    if (v.toUpperCase() == 'BR' || v == dialCode || v == '55' || v == '+55') {
      return isoCode;
    }
    // Sempre BR neste produto — evita initialSelection="" cair em KP (+850).
    return isoCode;
  }

  /// Sempre 'BR' para CountryCodePicker.initialSelection (nunca string vazia).
  static const String pickerInitialSelection = isoCode;

  /// Formatters para campos de telefone BR (DDI separado no CountryCodePicker).
  static List<TextInputFormatter> inputFormatters() => [
        FilteringTextInputFormatter.digitsOnly,
        BrazilPhoneInputFormatter(),
        LengthLimitingTextInputFormatter(15), // (XX) XXXXX-XXXX
      ];
}

/// Máscara progressiva: (XX) XXXX-XXXX ou (XX) 9XXXX-XXXX.
class BrazilPhoneInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = BrazilPhone.digitsOnly(newValue.text);
    final clipped = digits.length > 11 ? digits.substring(0, 11) : digits;
    final formatted = BrazilPhone.format(clipped);
    return TextEditingValue(
      text: formatted,
      selection: TextSelection.collapsed(offset: formatted.length),
    );
  }
}
