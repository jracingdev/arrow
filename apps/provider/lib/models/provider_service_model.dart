class ProviderServiceModel {
  String id;
  String author;
  String authorName;
  String title;
  String description;
  String price;
  String disPrice;
  String priceUnit;
  bool publish;
  List<dynamic> photos;
  String phoneNumber;
  String address;
  String sectionId;
  String categoryId;

  ProviderServiceModel({
    this.id = '',
    this.author = '',
    this.authorName = '',
    this.title = '',
    this.description = '',
    this.price = '',
    this.disPrice = '0',
    this.priceUnit = '',
    this.publish = true,
    this.photos = const [],
    this.phoneNumber = '',
    this.address = '',
    this.sectionId = '',
    this.categoryId = '',
  });

  factory ProviderServiceModel.fromJson(Map<String, dynamic> json) {
    return ProviderServiceModel(
      id: json['id']?.toString() ?? '',
      author: json['author']?.toString() ?? '',
      authorName: json['authorName']?.toString() ?? '',
      title: json['title']?.toString() ?? '',
      description: json['description']?.toString() ?? '',
      price: json['price']?.toString() ?? '',
      disPrice: json['disPrice']?.toString() ?? '0',
      priceUnit: json['priceUnit']?.toString() ?? '',
      publish: json['publish'] != false,
      photos: json['photos'] is List ? json['photos'] as List<dynamic> : const [],
      phoneNumber: json['phoneNumber']?.toString() ?? '',
      address: json['address']?.toString() ?? '',
      sectionId: json['sectionId']?.toString() ?? '',
      categoryId: json['categoryId']?.toString() ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'author': author,
      'authorName': authorName,
      'title': title,
      'description': description,
      'price': price,
      'disPrice': disPrice,
      'priceUnit': priceUnit,
      'publish': publish,
      'photos': photos,
      'phoneNumber': phoneNumber,
      'address': address,
      'sectionId': sectionId,
      'categoryId': categoryId,
    };
  }
}
