import 'package:cloud_firestore/cloud_firestore.dart';

/// Anexo de NFS-e da reserva (`provider_orders.invoices[]`).
class OrderInvoiceModel {
  static const String typeNfse = 'nfs-e';

  String type;
  String url;
  String fileName;
  Timestamp? uploadedAt;
  String uploadedBy;

  OrderInvoiceModel({
    this.type = typeNfse,
    this.url = '',
    this.fileName = '',
    this.uploadedAt,
    this.uploadedBy = '',
  });

  bool get hasFile => url.trim().isNotEmpty;

  factory OrderInvoiceModel.fromJson(Map<String, dynamic> json) {
    return OrderInvoiceModel(
      type: json['type']?.toString() ?? typeNfse,
      url: json['url']?.toString() ?? '',
      fileName: json['fileName']?.toString() ?? '',
      uploadedAt: json['uploadedAt'] is Timestamp ? json['uploadedAt'] as Timestamp : null,
      uploadedBy: json['uploadedBy']?.toString() ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'type': type,
      'url': url,
      'fileName': fileName,
      'uploadedAt': uploadedAt,
      'uploadedBy': uploadedBy,
    };
  }

  static List<OrderInvoiceModel> listFrom(dynamic raw) {
    if (raw is! List) return const [];
    return raw
        .whereType<Map>()
        .map((e) => OrderInvoiceModel.fromJson(Map<String, dynamic>.from(e)))
        .where((e) => e.hasFile)
        .toList();
  }
}
