import 'package:arrow_shared/provider_listing_rank.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('closer provider ranks first', () {
    expect(
      ProviderListingRank.compare(
        distanceA: 1.2,
        distanceB: 4.0,
        verifiedA: false,
        verifiedB: true,
        ratingA: 3,
        ratingB: 5,
      ),
      lessThan(0),
    );
  });

  test('same distance prefers verified', () {
    expect(
      ProviderListingRank.compare(
        distanceA: 2,
        distanceB: 2,
        verifiedA: true,
        verifiedB: false,
        ratingA: 4,
        ratingB: 5,
      ),
      lessThan(0),
    );
  });

  test('same distance and trust prefers higher rating', () {
    expect(
      ProviderListingRank.compare(
        distanceA: 3,
        distanceB: 3,
        verifiedA: true,
        verifiedB: true,
        ratingA: 4.8,
        ratingB: 4.1,
      ),
      lessThan(0),
    );
  });
}
