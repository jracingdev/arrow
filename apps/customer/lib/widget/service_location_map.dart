import 'package:arrow_shared/geo_distance.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:map_launcher/map_launcher.dart';
import 'package:url_launcher/url_launcher.dart';
import '../utils/utils.dart';

class ServiceLocationMap extends StatelessWidget {
  const ServiceLocationMap({
    super.key,
    required this.address,
    this.latitude,
    this.longitude,
    this.providerLat,
    this.providerLng,
    this.showProvider = false,
    this.isDark = false,
  });

  final String address;
  final double? latitude;
  final double? longitude;
  final double? providerLat;
  final double? providerLng;
  final bool showProvider;
  final bool isDark;

  bool get _hasDest => GeoDistance.isValid(latitude, longitude);

  Future<void> _open() async {
    if (_hasDest) {
      try {
        await Utils.redirectMap(
          name: address.isEmpty ? 'Local do serviço' : address,
          latitude: latitude!,
          longLatitude: longitude!,
        );
        return;
      } catch (_) {}
      try {
        final maps = await MapLauncher.installedMaps;
        if (maps.isNotEmpty) {
          await maps.first.showDirections(
            destination: Coords(latitude!, longitude!),
            destinationTitle: address,
            directionsMode: DirectionsMode.driving,
          );
          return;
        }
      } catch (_) {}
      final web = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=$latitude,$longitude');
      await launchUrl(web, mode: LaunchMode.externalApplication);
      return;
    }
    if (address.trim().isEmpty) {
      ShowToastDialog.showToast('Endereço indisponível'.tr);
      return;
    }
    final web = Uri.parse('https://www.google.com/maps/search/?api=1&query=${Uri.encodeComponent(address)}');
    await launchUrl(web, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    if (!_hasDest && address.isEmpty) return const SizedBox.shrink();
    final border = isDark ? const Color(0xFF334155) : const Color(0xFFE2E8F0);
    final bg = isDark ? const Color(0xFF0F172A) : const Color(0xFFF8FAFC);
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: border),
        color: bg,
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          if (_hasDest)
            SizedBox(
              height: 180,
              child: GoogleMap(
                initialCameraPosition: CameraPosition(target: LatLng(latitude!, longitude!), zoom: 15),
                markers: {
                  Marker(
                    markerId: const MarkerId('service'),
                    position: LatLng(latitude!, longitude!),
                    infoWindow: InfoWindow(title: address.isEmpty ? 'Local do serviço'.tr : address),
                  ),
                  if (showProvider && providerLat != null && providerLng != null)
                    Marker(
                      markerId: const MarkerId('provider'),
                      position: LatLng(providerLat!, providerLng!),
                      infoWindow: InfoWindow(title: 'Prestador'.tr),
                      icon: BitmapDescriptor.defaultMarkerWithHue(BitmapDescriptor.hueAzure),
                    ),
                },
                liteModeEnabled: true,
                zoomControlsEnabled: false,
                myLocationButtonEnabled: false,
                compassEnabled: false,
                mapToolbarEnabled: false,
              ),
            ),
          Padding(
            padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (address.isNotEmpty) Text(address),
                if (showProvider && providerLat != null) const SizedBox(height: 6),
                if (showProvider && providerLat != null)
                  Text('Prestador a caminho'.tr, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                const SizedBox(height: 8),
                OutlinedButton.icon(
                  onPressed: _open,
                  icon: const Icon(Icons.map_outlined),
                  label: Text('Abrir no mapa'.tr),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
