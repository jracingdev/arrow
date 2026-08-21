import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:provider/models/order_invoice_model.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/models/user_model.dart';

class ProviderOrderModel {
  String id;
  String authorID;
  String paymentMethod;
  String status;
  String notes;
  String workerId;
  String otp;
  String extraCharges;
  String extraChargesDescription;
  String reason;
  double quantity;
  bool? paymentStatus;
  bool? extraPaymentStatus;
  Timestamp? createdAt;
  Timestamp? scheduleDateTime;
  Timestamp? newScheduleDateTime;
  Timestamp? startTime;
  Timestamp? endTime;
  UserModel author;
  ProviderServiceModel provider;
  Map<String, dynamic>? address;
  List<OrderInvoiceModel> invoices;
  String dispatchMode;
  String requestedCategoryId;
  double radiusKm;
  List<String> rejectedBy;

  ProviderOrderModel({
    this.id = '',
    this.authorID = '',
    this.paymentMethod = '',
    this.status = '',
    this.notes = '',
    this.workerId = '',
    this.otp = '',
    this.extraCharges = '',
    this.extraChargesDescription = '',
    this.reason = '',
    this.quantity = 0,
    this.paymentStatus,
    this.extraPaymentStatus,
    this.createdAt,
    this.scheduleDateTime,
    this.newScheduleDateTime,
    this.startTime,
    this.endTime,
    UserModel? author,
    ProviderServiceModel? provider,
    this.address,
    List<OrderInvoiceModel>? invoices,
    this.dispatchMode = '',
    this.requestedCategoryId = '',
    this.radiusKm = 0,
    List<String>? rejectedBy,
  })  : author = author ?? UserModel(),
        provider = provider ?? ProviderServiceModel(),
        invoices = invoices ?? const [],
        rejectedBy = rejectedBy ?? const [];

  factory ProviderOrderModel.fromJson(Map<String, dynamic> json) {
    return ProviderOrderModel(
      id: json['id']?.toString() ?? '',
      authorID: json['authorID']?.toString() ?? '',
      paymentMethod: json['payment_method']?.toString() ?? '',
      status: json['status']?.toString() ?? '',
      notes: json['notes']?.toString() ?? '',
      workerId: json['workerId']?.toString() ?? '',
      otp: json['otp']?.toString() ?? '',
      extraCharges: json['extraCharges']?.toString() ?? '',
      extraChargesDescription: json['extraChargesDescription']?.toString() ?? '',
      reason: json['reason']?.toString() ?? '',
      quantity: double.tryParse('${json['quantity'] ?? 0}') ?? 0,
      paymentStatus: json['paymentStatus'] as bool?,
      extraPaymentStatus: json['extraPaymentStatus'] as bool?,
      createdAt: json['createdAt'] is Timestamp ? json['createdAt'] as Timestamp : null,
      scheduleDateTime: json['scheduleDateTime'] is Timestamp ? json['scheduleDateTime'] as Timestamp : null,
      newScheduleDateTime: json['newScheduleDateTime'] is Timestamp ? json['newScheduleDateTime'] as Timestamp : null,
      startTime: json['startTime'] is Timestamp ? json['startTime'] as Timestamp : null,
      endTime: json['endTime'] is Timestamp ? json['endTime'] as Timestamp : null,
      author: json['author'] is Map ? UserModel.fromJson(Map<String, dynamic>.from(json['author'] as Map)) : UserModel(),
      provider: json['provider'] is Map
          ? ProviderServiceModel.fromJson(Map<String, dynamic>.from(json['provider'] as Map))
          : ProviderServiceModel(),
      address: json['address'] is Map ? Map<String, dynamic>.from(json['address'] as Map) : null,
      invoices: OrderInvoiceModel.listFrom(json['invoices']),
      dispatchMode: json['dispatchMode']?.toString() ?? '',
      requestedCategoryId: json['requestedCategoryId']?.toString() ?? '',
      radiusKm: double.tryParse('${json['radiusKm'] ?? 0}') ?? 0,
      rejectedBy: json['rejectedBy'] is List
          ? List<String>.from((json['rejectedBy'] as List).map((e) => e.toString()))
          : const [],
    );
  }

  bool get isBroadcast => dispatchMode == 'broadcast';

  bool get hasAssignedProvider => provider.author.trim().isNotEmpty;

  String addressLine() {
    if (address == null) return '';
    final parts = [
      address!['address'] ?? address!['locality'] ?? address!['addressAsString'],
      address!['landmark'],
    ].where((e) => e != null && e.toString().trim().isNotEmpty).map((e) => e.toString());
    return parts.join(' · ');
  }

  double? customerLat() {
    final loc = address?['location'];
    if (loc is Map) {
      return double.tryParse('${loc['latitude'] ?? ''}');
    }
    return double.tryParse('${address?['latitude'] ?? ''}');
  }

  double? customerLng() {
    final loc = address?['location'];
    if (loc is Map) {
      return double.tryParse('${loc['longitude'] ?? ''}');
    }
    return double.tryParse('${address?['longitude'] ?? ''}');
  }
}
