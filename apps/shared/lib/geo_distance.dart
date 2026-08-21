import 'dart:math';

/// Distância Haversine e geohash (mesmo alfabeto do Geoflutterfire).
class GeoDistance {
  GeoDistance._();

  static const earthRadiusKm = 6371.0088;
  static const _base32 = '0123456789bcdefghjkmnpqrstuvwxyz';

  /// Trata 0 / 0.1 (placeholder dos models on-demand) como coordenada ausente.
  static bool isValid(double? lat, double? lng) {
    if (lat == null || lng == null) return false;
    if (!lat.isFinite || !lng.isFinite) return false;
    if (lat.abs() < 0.2 && lng.abs() < 0.2) return false;
    return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
  }

  static double _rad(double deg) => deg * (pi / 180.0);

  /// Distância em km, ou `null` se alguma coordenada for inválida.
  static double? km({
    required double? fromLat,
    required double? fromLng,
    required double? toLat,
    required double? toLng,
  }) {
    if (!isValid(fromLat, fromLng) || !isValid(toLat, toLng)) return null;
    final dLat = _rad(toLat! - fromLat!);
    final dLng = _rad(toLng! - fromLng!);
    final a = sin(dLat / 2) * sin(dLat / 2) +
        cos(_rad(fromLat)) * cos(_rad(toLat)) * sin(dLng / 2) * sin(dLng / 2);
    final c = 2 * atan2(sqrt(a), sqrt(1 - a));
    return earthRadiusKm * c;
  }

  /// Ex.: `1,2 km` (pt-BR).
  static String formatKm(double? kilometers, {int fractionDigits = 1}) {
    if (kilometers == null || !kilometers.isFinite) return '';
    final value = kilometers.toStringAsFixed(fractionDigits).replaceAll('.', ',');
    return '$value km';
  }

  static int compareKm(double? a, double? b) {
    if (a == null && b == null) return 0;
    if (a == null) return 1;
    if (b == null) return -1;
    return a.compareTo(b);
  }

  /// Primeira coordenada válida na ordem de preferência do produto.
  static ({double lat, double lng})? resolve({
    double? serviceLat,
    double? serviceLng,
    double? geoLat,
    double? geoLng,
    double? userLat,
    double? userLng,
    double? vendorLat,
    double? vendorLng,
  }) {
    if (isValid(serviceLat, serviceLng)) return (lat: serviceLat!, lng: serviceLng!);
    if (isValid(geoLat, geoLng)) return (lat: geoLat!, lng: geoLng!);
    if (isValid(userLat, userLng)) return (lat: userLat!, lng: userLng!);
    if (isValid(vendorLat, vendorLng)) return (lat: vendorLat!, lng: vendorLng!);
    return null;
  }

  static String geohash(double latitude, double longitude, {int precision = 9}) {
    var chars = <String>[];
    var bits = 0;
    var bitsTotal = 0;
    var hashValue = 0;
    double maxLat = 90;
    double minLat = -90;
    double maxLon = 180;
    double minLon = -180;

    while (chars.length < precision) {
      if (bitsTotal % 2 == 0) {
        final mid = (maxLon + minLon) / 2;
        if (longitude > mid) {
          hashValue = (hashValue << 1) + 1;
          minLon = mid;
        } else {
          hashValue = (hashValue << 1) + 0;
          maxLon = mid;
        }
      } else {
        final mid = (maxLat + minLat) / 2;
        if (latitude > mid) {
          hashValue = (hashValue << 1) + 1;
          minLat = mid;
        } else {
          hashValue = (hashValue << 1) + 0;
          maxLat = mid;
        }
      }
      bits++;
      bitsTotal++;
      if (bits == 5) {
        chars.add(_base32[hashValue]);
        bits = 0;
        hashValue = 0;
      }
    }
    return chars.join();
  }
}
