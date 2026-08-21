import 'package:cloud_firestore/cloud_firestore.dart';

class UserModel {
  String? id;
  String? firstName;
  String? lastName;
  String? email;
  String? profilePictureURL;
  String? fcmToken;
  String? countryCode;
  String? countryISOCode;
  String? phoneNumber;
  num? walletAmount;
  bool? active;
  Timestamp? createdAt;
  String? role;
  String? provider;
  String? sectionId;
  String? appIdentifier;
  bool? isDocumentVerify;
  bool? isAutoVerify;
  num? reviewsCount;
  num? reviewsSum;
  Map<String, dynamic>? location;
  double? latitude;
  double? longitude;

  UserModel({
    this.id,
    this.firstName,
    this.lastName,
    this.email,
    this.profilePictureURL,
    this.fcmToken,
    this.countryCode,
    this.countryISOCode,
    this.phoneNumber,
    this.walletAmount,
    this.active,
    this.createdAt,
    this.role,
    this.provider,
    this.sectionId,
    this.appIdentifier,
    this.isDocumentVerify,
    this.isAutoVerify,
    this.reviewsCount,
    this.reviewsSum,
    this.location,
    this.latitude,
    this.longitude,
  });

  String fullName() => '${firstName ?? ''} ${lastName ?? ''}'.trim();

  bool get needsDocumentVerification => isDocumentVerify != true && isAutoVerify != true;

  UserModel.fromJson(Map<String, dynamic> json) {
    id = json['id']?.toString() ?? json['userID']?.toString();
    email = json['email']?.toString();
    firstName = json['firstName']?.toString();
    lastName = json['lastName']?.toString();
    profilePictureURL = json['profilePictureURL']?.toString();
    fcmToken = json['fcmToken']?.toString();
    countryCode = json['countryCode']?.toString();
    countryISOCode = json['countryISOCode']?.toString();
    phoneNumber = json['phoneNumber']?.toString();
    walletAmount = num.tryParse(json['wallet_amount']?.toString() ?? '0') ?? 0;
    active = json['active'] == true || json['isActive'] == true;
    createdAt = json['createdAt'] is Timestamp ? json['createdAt'] as Timestamp : null;
    role = json['role']?.toString();
    provider = json['provider']?.toString();
    sectionId = json['sectionId']?.toString() ?? json['section_id']?.toString();
    appIdentifier = json['appIdentifier']?.toString();
    isDocumentVerify = json['isDocumentVerify'] == true;
    isAutoVerify = json['isAutoVerify'] == true;
    reviewsCount = num.tryParse(json['reviewsCount']?.toString() ?? '0') ?? 0;
    reviewsSum = num.tryParse(json['reviewsSum']?.toString() ?? '0') ?? 0;
    if (json['location'] is Map) {
      location = Map<String, dynamic>.from(json['location'] as Map);
    }
    latitude = double.tryParse('${json['latitude'] ?? ''}');
    longitude = double.tryParse('${json['longitude'] ?? ''}');
    if (location != null) {
      latitude ??= double.tryParse('${location!['latitude'] ?? ''}');
      longitude ??= double.tryParse('${location!['longitude'] ?? ''}');
    }
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'userID': id,
      'email': email,
      'firstName': firstName,
      'lastName': lastName,
      'profilePictureURL': profilePictureURL,
      'fcmToken': fcmToken,
      'countryCode': countryCode,
      'countryISOCode': countryISOCode,
      'phoneNumber': phoneNumber,
      'wallet_amount': walletAmount ?? 0,
      'active': active ?? false,
      'isActive': active ?? false,
      'createdAt': createdAt,
      'role': role,
      'provider': provider,
      'sectionId': sectionId,
      'appIdentifier': appIdentifier,
      'isDocumentVerify': isDocumentVerify ?? false,
      'isAutoVerify': isAutoVerify ?? false,
      'reviewsCount': reviewsCount ?? 0,
      'reviewsSum': reviewsSum ?? 0,
      'latitude': latitude,
      'longitude': longitude,
      if (latitude != null && longitude != null)
        'location': {'latitude': latitude, 'longitude': longitude},
    };
  }
}
