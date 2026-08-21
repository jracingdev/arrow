import 'package:map_launcher/map_launcher.dart';
import 'package:url_launcher/url_launcher.dart';

/// Deep-link turn-by-turn — same Arrow pattern as the driver app (`map_launcher`).
class ServiceNavigation {
  ServiceNavigation._();

  static Future<void> open({
    required String name,
    double? latitude,
    double? longitude,
    String address = '',
  }) async {
    if (latitude != null && longitude != null) {
      try {
        final available = await MapLauncher.installedMaps;
        if (available.isNotEmpty) {
          var map = available.first;
          for (final candidate in available) {
            if (candidate.mapType == MapType.google || candidate.mapType == MapType.waze || candidate.mapType == MapType.apple) {
              map = candidate;
              break;
            }
          }
          await map.showDirections(
            destination: Coords(latitude, longitude),
            destinationTitle: name.isEmpty ? address : name,
            directionsMode: DirectionsMode.driving,
          );
          return;
        }
      } catch (_) {}
      final geo = Uri.parse('google.navigation:q=$latitude,$longitude');
      if (await canLaunchUrl(geo)) {
        await launchUrl(geo, mode: LaunchMode.externalApplication);
        return;
      }
      final web = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=$latitude,$longitude');
      await launchUrl(web, mode: LaunchMode.externalApplication);
      return;
    }

    final query = address.trim().isEmpty ? name : address.trim();
    if (query.isEmpty) return;
    final web = Uri.parse('https://www.google.com/maps/search/?api=1&query=${Uri.encodeComponent(query)}');
    await launchUrl(web, mode: LaunchMode.externalApplication);
  }
}
