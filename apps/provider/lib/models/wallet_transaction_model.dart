import 'package:cloud_firestore/cloud_firestore.dart';

class WalletTransactionModel {
  String? userId;
  String? paymentMethod;
  double? amount;
  bool? isTopup;
  String? orderId;
  String? paymentStatus;
  Timestamp? date;
  String? id;
  String? transactionUser;
  String? note;
  String? serviceType;

  WalletTransactionModel({
    this.userId,
    this.paymentMethod,
    this.amount,
    this.isTopup,
    this.orderId,
    this.paymentStatus,
    this.date,
    this.id,
    this.transactionUser,
    this.note,
    this.serviceType,
  });

  WalletTransactionModel.fromJson(Map<String, dynamic> json) {
    id = json['id']?.toString();
    userId = json['user_id']?.toString();
    paymentMethod = json['payment_method']?.toString();
    amount = double.tryParse('${json['amount'] ?? 0}') ?? 0;
    isTopup = json['isTopUp'] == true;
    orderId = json['order_id']?.toString();
    paymentStatus = json['payment_status']?.toString();
    date = json['date'] is Timestamp ? json['date'] as Timestamp : null;
    transactionUser = json['transactionUser']?.toString() ?? 'provider';
    note = json['note']?.toString() ?? '';
    serviceType = json['serviceType']?.toString();
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'payment_method': paymentMethod,
      'amount': amount,
      'isTopUp': isTopup,
      'order_id': orderId,
      'payment_status': paymentStatus,
      'date': date,
      'transactionUser': transactionUser,
      'note': note,
      'serviceType': serviceType,
    };
  }
}
