/// Texto e link estáticos para compartilhar o local do atendimento (segurança).
class ShareLocationMessage {
  ShareLocationMessage._();

  static const assigned = 'Order Assigned';
  static const ongoing = 'Order Ongoing';
  static const accepted = 'Order Accepted';
  static const inTransit = 'In Transit';

  static bool isLiveJob(String? status) {
    return status == assigned || status == ongoing || status == accepted || status == inTransit;
  }

  static String mapsUrl({double? lat, double? lng}) {
    if (lat == null || lng == null) return '';
    if (!lat.isFinite || !lng.isFinite) return '';
    if (lat.abs() < 0.2 && lng.abs() < 0.2) return '';
    return 'https://www.google.com/maps/search/?api=1&query=$lat,$lng';
  }

  static String build({
    required String who,
    required String address,
    String schedule = '',
    double? lat,
    double? lng,
    bool liveEnRoute = false,
    bool fromProvider = false,
  }) {
    final maps = mapsUrl(lat: lat, lng: lng);
    final lines = <String>[
      fromProvider ? 'Estou em um atendimento Arrow.' : 'Compartilhei o local do meu atendimento Arrow.',
      if (who.trim().isNotEmpty) 'Quem: ${who.trim()}',
      if (address.trim().isNotEmpty) 'Endereço: ${address.trim()}',
      if (schedule.trim().isNotEmpty) 'Horário: ${schedule.trim()}',
      if (liveEnRoute) 'O profissional está a caminho ou no local.',
      if (maps.isNotEmpty) 'Mapa: $maps',
    ];
    return lines.join('\n');
  }
}
