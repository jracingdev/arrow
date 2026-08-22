import 'package:arrow_shared/arrow_currency.dart';
import 'package:cloud_firestore/cloud_firestore.dart';

class CurrencyModel {
  Timestamp? createdAt;
  String? symbol;
  String? code;
  bool? enable;
  bool? symbolAtRight;
  String? name;
  int? decimalDigits;
  String? id;
  Timestamp? updatedAt;

  CurrencyModel({this.createdAt, this.symbol, this.code, this.enable, this.symbolAtRight, this.name, this.decimalDigits, this.id, this.updatedAt});

  CurrencyModel.fromJson(Map<String, dynamic> json) {
    createdAt = json['createdAt'];
    symbol = ArrowCurrency.parseSymbol(json);
    code = ArrowCurrency.parseCode(json);
    enable = json['enable'] == true || json['isActive'] == true;
    symbolAtRight = ArrowCurrency.parseSymbolAtRight(json);
    name = json['name'];
    decimalDigits = ArrowCurrency.parseDecimals(json);
    id = json['id'];
    updatedAt = json['updatedAt'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['createdAt'] = createdAt;
    data['symbol'] = symbol;
    data['code'] = code;
    data['enable'] = enable;
    data['symbolAtRight'] = symbolAtRight;
    data['name'] = name;
    data['decimalDigits'] = decimalDigits;
    data['id'] = id;
    data['updatedAt'] = updatedAt;
    return data;
  }
}
