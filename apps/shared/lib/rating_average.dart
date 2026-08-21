/// Média de avaliações a partir de `reviewsSum` / `reviewsCount`.
class RatingAverage {
  RatingAverage._();

  static double of(num? sum, num? count) {
    final c = (count ?? 0).toDouble();
    if (c <= 0) return 0;
    return (sum ?? 0).toDouble() / c;
  }

  static String formatted(num? sum, num? count, {int decimals = 1}) {
    return of(sum, count).toStringAsFixed(decimals);
  }
}
