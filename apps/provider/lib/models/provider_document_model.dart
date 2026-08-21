class ProviderDocumentModel {
  List<UploadedDocument>? documents;
  String? id;
  String? type;
  bool? pending;
  String? rejectReason;

  ProviderDocumentModel({this.documents, this.id, this.type, this.pending, this.rejectReason});

  ProviderDocumentModel.fromJson(Map<String, dynamic> json) {
    if (json['documents'] != null) {
      documents = <UploadedDocument>[];
      for (final v in json['documents'] as List) {
        if (v is Map) {
          documents!.add(UploadedDocument.fromJson(Map<String, dynamic>.from(v)));
        }
      }
    }
    id = json['id']?.toString();
    type = json['type']?.toString();
    pending = json['pending'] == true;
    rejectReason = json['rejectReason']?.toString();
  }

  bool get hasRejected => documents?.any((d) => (d.status ?? '').toLowerCase() == 'rejected') == true;

  String? get latestRejectReason {
    for (final d in documents ?? const []) {
      if ((d.status ?? '').toLowerCase() == 'rejected' && (d.rejectReason?.isNotEmpty == true)) {
        return d.rejectReason;
      }
    }
    if (rejectReason?.isNotEmpty == true) return rejectReason;
    return null;
  }

  Map<String, dynamic> toJson() {
    return {
      'documents': documents?.map((e) => e.toJson()).toList() ?? [],
      'id': id,
      'type': type,
      'pending': pending ?? true,
      'rejectReason': rejectReason ?? '',
    };
  }
}

class UploadedDocument {
  String? frontImage;
  String? status;
  String? documentId;
  String? backImage;
  String? rejectReason;

  UploadedDocument({this.frontImage, this.status, this.documentId, this.backImage, this.rejectReason});

  UploadedDocument.fromJson(Map<String, dynamic> json) {
    frontImage = json['frontImage']?.toString();
    status = json['status']?.toString();
    documentId = json['documentId']?.toString();
    backImage = json['backImage']?.toString();
    rejectReason = json['rejectReason']?.toString();
  }

  Map<String, dynamic> toJson() {
    return {
      'frontImage': frontImage,
      'status': status,
      'documentId': documentId,
      'backImage': backImage,
      'rejectReason': rejectReason ?? '',
    };
  }
}
