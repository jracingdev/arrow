import 'package:arrow_shared/geo_distance.dart';

/// Customer listing order: closer first, then verified, then higher rating.
class ProviderListingRank {
  ProviderListingRank._();

  static int compare({
    required double? distanceA,
    required double? distanceB,
    required bool verifiedA,
    required bool verifiedB,
    required double ratingA,
    required double ratingB,
  }) {
    final byDistance = GeoDistance.compareKm(distanceA, distanceB);
    if (byDistance != 0) return byDistance;
    if (verifiedA != verifiedB) return verifiedA ? -1 : 1;
    return ratingB.compareTo(ratingA);
  }
}
