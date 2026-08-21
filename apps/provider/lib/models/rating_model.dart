import 'package:cloud_firestore/cloud_firestore.dart';

class RatingModel {
  String? id;
  double? rating;
  String? comment;
  String? orderId;
  String? customerId;
  String? vendorId;
  String? productId;
  String? uname;
  Timestamp? createdAt;

  RatingModel({
    this.id,
    this.rating,
    this.comment,
    this.orderId,
    this.customerId,
    this.vendorId,
    this.productId,
    this.uname,
    this.createdAt,
  });

  factory RatingModel.fromJson(Map<String, dynamic> json) {
    return RatingModel(
      id: json['Id']?.toString() ?? json['id']?.toString(),
      rating: double.tryParse('${json['rating'] ?? 0}') ?? 0,
      comment: json['comment']?.toString() ?? '',
      orderId: json['orderid']?.toString() ?? json['orderId']?.toString(),
      customerId: json['CustomerId']?.toString() ?? json['customerId']?.toString(),
      vendorId: json['VendorId']?.toString() ?? json['vendorId']?.toString(),
      productId: json['productId']?.toString(),
      uname: json['uname']?.toString() ?? '',
      createdAt: json['createdAt'] is Timestamp ? json['createdAt'] as Timestamp : null,
    );
  }
}
