import 'package:cloud_firestore/cloud_firestore.dart';

class SubscriptionPlanModel {
  Timestamp? createdAt;
  String? description;
  String? expiryDay;
  Features? features;
  String? id;
  bool? isEnable;
  bool? isCommissionPlan;
  String? itemLimit;
  String? orderLimit;
  String? name;
  String? price;
  String? place;
  String? image;
  String? type;
  String? sectionId;
  List<String>? planPoints;

  SubscriptionPlanModel(
      {this.createdAt,
        this.description,
        this.expiryDay,
        this.features,
        this.id,
        this.isEnable,
        this.isCommissionPlan,
        this.itemLimit,
        this.orderLimit,
        this.name,
        this.price,
        this.place,
        this.image,
        this.type,
        this.sectionId,
        this.planPoints});

  factory SubscriptionPlanModel.fromJson(Map<String, dynamic> json) {
    return SubscriptionPlanModel(
      createdAt: json['createdAt'],
      description: json['description']?.toString(),
      expiryDay: json['expiryDay']?.toString(),
      features: json['features'] == null ? null : Features.fromJson(Map<String, dynamic>.from(json['features'])),
      id: json['id']?.toString(),
      isEnable: json['isEnable'] == true,
      isCommissionPlan: json['isCommissionPlan'] == true,
      itemLimit: json['itemLimit']?.toString(),
      orderLimit: json['orderLimit']?.toString(),
      name: json['name']?.toString(),
      price: () {
        final raw = json['price'];
        if (raw == null) return '0.00';
        return (double.tryParse(raw.toString()) ?? 0).toStringAsFixed(2);
      }(),
      sectionId: json['sectionId']?.toString(),
      image: json['image']?.toString(),
      type: json['type']?.toString(),
      planPoints: json['plan_points'] == null ? [] : List<String>.from(json['plan_points']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'createdAt': createdAt,
      'description': description,
      'expiryDay': expiryDay.toString(),
      'features': features?.toJson(),
      'id': id,
      'isEnable': isEnable,
      'itemLimit': itemLimit.toString(),
      'orderLimit': orderLimit.toString(),
      'name': name,
      'price': price.toString(),
      'place': place.toString(),
      'image': image.toString(),
      'type': type,
      'sectionId': sectionId,
      'plan_points': planPoints
    };
  }
}

class Features {
  bool? chat;
  bool? qrCodeGenerate;
  bool? ownerMobileApp;
  bool? demo;

  Features({
    this.chat,
    this.qrCodeGenerate,
    this.ownerMobileApp,
    this.demo,
  });

  // Factory constructor to create an instance from JSON
  factory Features.fromJson(Map<String, dynamic> json) {
    return Features(
      chat: json['chat'] ?? false,
      qrCodeGenerate: json['qrCodeGenerate'] ?? false,
      ownerMobileApp: json['ownerMobileApp'] ?? false,
    );
  }

  // Method to convert an instance to JSON
  Map<String, dynamic> toJson() {
    return {
      'chat': chat,
      'qrCodeGenerate': qrCodeGenerate,
      'ownerMobileApp': ownerMobileApp,
    };
  }
}