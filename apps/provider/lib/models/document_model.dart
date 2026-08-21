class DocumentModel {
  bool? backSide;
  bool? enable;
  bool? expireAt;
  String? id;
  bool? frontSide;
  String? title;
  String? type;

  DocumentModel({
    this.backSide,
    this.enable,
    this.id,
    this.frontSide,
    this.title,
    this.expireAt,
    this.type,
  });

  DocumentModel.fromJson(Map<String, dynamic> json) {
    backSide = json['backSide'] == true;
    enable = json['enable'] == true;
    id = json['id']?.toString();
    frontSide = json['frontSide'] == true;
    title = json['title']?.toString();
    expireAt = json['expireAt'] == true;
    type = json['type']?.toString();
  }
}
