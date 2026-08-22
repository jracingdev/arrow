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
  bool online = false;
  String? address;
  String? cep;
  List<Map<String, dynamic>> shippingAddress = const [];

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
    this.online = false,
    this.address,
    this.cep,
    this.shippingAddress = const [],
  });

  String profileAddressLine() {
    final direct = (address ?? '').trim();
    if (direct.isNotEmpty) return direct;
    for (final item in shippingAddress) {
      final line = [
        item['address'],
        item['addressAsString'],
        item['locality'],
        item['landmark'],
      ].map((e) => (e ?? '').toString().trim()).where((e) => e.isNotEmpty).join(', ');
      if (line.isNotEmpty) return line;
    }
    final loc = location;
    if (loc != null) {
      final line = [loc['address'], loc['locality']].map((e) => (e ?? '').toString().trim()).where((e) => e.isNotEmpty).join(', ');
      if (line.isNotEmpty) return line;
    }
    return '';
  }

  String profileCep() {
    final direct = (cep ?? '').trim();
    if (direct.isNotEmpty) return direct;
    for (final item in shippingAddress) {
      final value = (item['cep'] ?? item['postalCode'] ?? item['zipCode'] ?? '').toString().trim();
      if (value.isNotEmpty) return value;
    }
    return (location?['cep'] ?? location?['postalCode'] ?? '').toString().trim();
  }

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
    online = json['online'] == true;
    if (location != null) {
      latitude ??= double.tryParse('${location!['latitude'] ?? ''}');
      longitude ??= double.tryParse('${location!['longitude'] ?? ''}');
    }
    address = json['address']?.toString();
    cep = (json['cep'] ?? json['postalCode'] ?? json['zipCode'])?.toString();
    if (json['shippingAddress'] is List) {
      shippingAddress = (json['shippingAddress'] as List)
          .whereType<Map>()
          .map((e) => Map<String, dynamic>.from(e))
          .toList();
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
      'online': online,
      if (address != null) 'address': address,
      if (cep != null) 'cep': cep,
      if (shippingAddress.isNotEmpty) 'shippingAddress': shippingAddress,
      if (latitude != null && longitude != null)
        'location': {'latitude': latitude, 'longitude': longitude},
    };
  }
}
