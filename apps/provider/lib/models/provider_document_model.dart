class ProviderDocumentModel {
  List<UploadedDocument>? documents;
  String? id;
  String? type;
  bool? pending;

  ProviderDocumentModel({this.documents, this.id, this.type, this.pending});

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
  }

  Map<String, dynamic> toJson() {
    return {
      'documents': documents?.map((e) => e.toJson()).toList() ?? [],
      'id': id,
      'type': type,
      'pending': pending ?? true,
    };
  }
}

class UploadedDocument {
  String? frontImage;
  String? status;
  String? documentId;
  String? backImage;

  UploadedDocument({this.frontImage, this.status, this.documentId, this.backImage});

  UploadedDocument.fromJson(Map<String, dynamic> json) {
    frontImage = json['frontImage']?.toString();
    status = json['status']?.toString();
    documentId = json['documentId']?.toString();
    backImage = json['backImage']?.toString();
  }

  Map<String, dynamic> toJson() {
    return {
      'frontImage': frontImage,
      'status': status,
      'documentId': documentId,
      'backImage': backImage,
    };
  }
}
