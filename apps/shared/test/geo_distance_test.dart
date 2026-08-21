import 'package:arrow_shared/geo_distance.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('rejects placeholder and out-of-range coordinates', () {
    expect(GeoDistance.isValid(0, 0), isFalse);
    expect(GeoDistance.isValid(0.1, 0.1), isFalse);
    expect(GeoDistance.isValid(-23.55, -46.63), isTrue);
    expect(GeoDistance.isValid(91, 0), isFalse);
  });

  test('haversine sorts nearest first and formats km in pt-BR', () {
    const originLat = -23.5505;
    const originLng = -46.6333;
    final near = GeoDistance.km(fromLat: originLat, fromLng: originLng, toLat: -23.555, toLng: -46.640);
    final far = GeoDistance.km(fromLat: originLat, fromLng: originLng, toLat: -22.9068, toLng: -43.1729);
    expect(near, isNotNull);
    expect(far, isNotNull);
    expect(near!, lessThan(2));
    expect(far!, greaterThan(300));
    expect(GeoDistance.compareKm(near, far), lessThan(0));
    expect(GeoDistance.formatKm(1.23), '1,2 km');
  });

  test('resolves provider coordinates in preference order', () {
    final coords = GeoDistance.resolve(
      serviceLat: -23.55,
      serviceLng: -46.63,
      userLat: -22.9,
      userLng: -43.17,
    );
    expect(coords?.lat, -23.55);
    expect(GeoDistance.resolve(serviceLat: 0.1, serviceLng: 0.1, userLat: -22.9, userLng: -43.17)?.lat, -22.9);
  });

  test('geohash matches geoflutterfire length 9', () {
    final hash = GeoDistance.geohash(-23.5505, -46.6333);
    expect(hash.length, 9);
    expect(hash, matches(RegExp(r'^[0-9bcdefghjkmnpqrstuvwxyz]+$')));
  });
}
