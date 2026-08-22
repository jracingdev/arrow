import 'package:arrow_shared/geo_distance.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:get/get.dart';
import 'package:map_launcher/map_launcher.dart';
import 'package:url_launcher/url_launcher.dart';

/// Mesmo deep link do prestador: Google Maps / Waze em navegação, sem chave Google.
class ServiceNavigation {
  ServiceNavigation._();

  static const noMapsToast = 'Instale Google Maps ou Waze';

  static Future<void> open({
    required String name,
    double? latitude,
    double? longitude,
    String address = '',
  }) async {
    final hasCoords = GeoDistance.isValid(latitude, longitude);
    final destination = hasCoords
        ? '${latitude!.toString()},${longitude!.toString()}'
        : (address.trim().isEmpty ? name.trim() : address.trim());

    if (destination.isEmpty) {
      ShowToastDialog.showToast(noMapsToast.tr);
      return;
    }

    final encoded = Uri.encodeComponent(destination);

    if (await _tryLaunch(Uri.parse('google.navigation:q=$encoded'))) return;

    if (hasCoords) {
      if (await _tryLaunch(Uri.parse('waze://?ll=${latitude!},${longitude!}&navigate=yes'))) return;
    } else if (await _tryLaunch(Uri.parse('waze://?q=$encoded&navigate=yes'))) {
      return;
    }

    if (await _tryLaunch(Uri.parse(
      'https://www.google.com/maps/dir/?api=1&destination=$encoded&travelmode=driving&dir_action=navigate',
    ))) {
      return;
    }

    if (hasCoords && await _tryMapLauncher(latitude!, longitude!, name, address)) return;

    ShowToastDialog.showToast(noMapsToast.tr);
  }

  static Future<bool> _tryLaunch(Uri uri) async {
    try {
      return await launchUrl(uri, mode: LaunchMode.externalApplication);
    } catch (_) {
      return false;
    }
  }

  static Future<bool> _tryMapLauncher(double latitude, double longitude, String name, String address) async {
    try {
      final available = await MapLauncher.installedMaps;
      AvailableMap? map;
      for (final candidate in available) {
        if (candidate.mapType == MapType.google || candidate.mapType == MapType.waze) {
          map = candidate;
          break;
        }
        if (map == null && (candidate.mapType == MapType.googleGo || candidate.mapType == MapType.apple)) {
          map = candidate;
        }
      }
      if (map == null) return false;
      await map.showDirections(
        destination: Coords(latitude, longitude),
        destinationTitle: name.isEmpty ? address : name,
        directionsMode: DirectionsMode.driving,
      );
      return true;
    } catch (_) {
      return false;
    }
  }
}
