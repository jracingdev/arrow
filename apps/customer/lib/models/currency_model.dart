import 'package:arrow_shared/arrow_currency.dart';

class CurrencyModel {
  String code;
  int decimal;
  String id;
  bool isactive;
  num rounding;
  String name;
  String symbol;
  bool symbolatright;

  CurrencyModel({
    this.code = '',
    this.decimal = 0,
    this.isactive = false,
    this.id = '',
    this.name = '',
    this.rounding = 0,
    this.symbol = '',
    this.symbolatright = false,
  });

  factory CurrencyModel.fromJson(Map<String, dynamic> parsedJson) {
    return CurrencyModel(
      code: ArrowCurrency.parseCode(parsedJson),
      decimal: ArrowCurrency.parseDecimals(parsedJson),
      isactive: parsedJson['isActive'] == true || parsedJson['enable'] == true,
      id: parsedJson['id']?.toString() ?? '',
      name: parsedJson['name']?.toString() ?? '',
      rounding: num.tryParse('${parsedJson['rounding'] ?? 0}') ?? 0,
      symbol: ArrowCurrency.parseSymbol(parsedJson),
      symbolatright: ArrowCurrency.parseSymbolAtRight(parsedJson),
    );
  }


  Map<String, dynamic> toJson() {
    return {
      'code': code,
      'decimal_degits': decimal,
      'isActive': isactive,
      'rounding': rounding,
      'id': id,
      'name': name,
      'symbol': symbol,
      'symbolAtRight': symbolatright,
    };
  }
}
