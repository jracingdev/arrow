import 'package:cloud_firestore/cloud_firestore.dart';

class ZoneModel {
  List<GeoPoint>? area;
  bool? publish;
  double? latitude;
  String? name;
  String? id;
  double? longitude;

  ZoneModel({this.area, this.publish, this.latitude, this.name, this.id, this.longitude});

  ZoneModel.fromJson(Map<String, dynamic> json) {
    if (json['area'] is List) {
      area = <GeoPoint>[];
      for (final v in json['area'] as List) {
        if (v is GeoPoint) {
          area!.add(v);
        }
      }
    }

    publish = json['publish'] == true;
    final lat = json['latitude'];
    latitude = lat is num ? lat.toDouble() : null;
    name = json['name']?.toString();
    id = json['id']?.toString();
    final lng = json['longitude'];
    longitude = lng is num ? lng.toDouble() : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (area != null) {
      data['area'] = area!.map((v) => v).toList();
    }
    data['publish'] = publish;
    data['latitude'] = latitude;
    data['name'] = name;
    data['id'] = id;
    data['longitude'] = longitude;
    return data;
  }
}
