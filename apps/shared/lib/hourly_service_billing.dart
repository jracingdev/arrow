/// Cobrança por hora de serviço On Demand (igual ao painel admin).
class HourlyServiceBilling {
  HourlyServiceBilling._();

  static bool isHourly(String? priceUnit) {
    final unit = (priceUnit ?? '').trim().toLowerCase();
    return unit == 'hourly' || unit == 'hour' || unit == 'por hora';
  }

  /// Horas faturadas: fração com 2 casas; mínimo 1 hora (regra do admin Arrow).
  static double billableHours(DateTime start, DateTime end) {
    final hours = end.difference(start).inMilliseconds / (1000 * 60 * 60);
    if (hours <= 0) return 1;
    if (hours < 1) return 1;
    return double.parse(hours.toStringAsFixed(2));
  }

  static String formatElapsed(Duration elapsed) {
    final safe = elapsed.isNegative ? Duration.zero : elapsed;
    final hours = safe.inHours;
    final minutes = safe.inMinutes.remainder(60);
    final seconds = safe.inSeconds.remainder(60);
    return '${hours.toString().padLeft(2, '0')}:'
        '${minutes.toString().padLeft(2, '0')}:'
        '${seconds.toString().padLeft(2, '0')}';
  }

  static double unitPrice(String price, String disPrice) {
    final discounted = double.tryParse(disPrice) ?? 0;
    if (discounted > 0) return discounted;
    return double.tryParse(price) ?? 0;
  }

  static double amount(double rate, double hours) => rate * hours;
}
