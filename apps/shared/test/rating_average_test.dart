import 'package:arrow_shared/rating_average.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('returns zero without reviews', () {
    expect(RatingAverage.of(null, null), 0);
    expect(RatingAverage.of(10, 0), 0);
    expect(RatingAverage.formatted(0, 0), '0.0');
  });

  test('divides sum by count', () {
    expect(RatingAverage.of(9, 2), 4.5);
    expect(RatingAverage.formatted(10, 2), '5.0');
  });
}
