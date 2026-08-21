import 'package:arrow_shared/geo_distance.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:get/get.dart';
import 'package:latlong2/latlong.dart';
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

  bool get _hasProvider => showProvider && GeoDistance.isValid(providerLat, providerLng);

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

  MapOptions _mapOptions() {
    final dest = LatLng(latitude!, longitude!);
    if (!_hasProvider) {
      return MapOptions(
        initialCenter: dest,
        initialZoom: 15,
        interactionOptions: const InteractionOptions(flags: InteractiveFlag.none),
      );
    }
    return MapOptions(
      initialCameraFit: CameraFit.bounds(
        bounds: LatLngBounds(dest, LatLng(providerLat!, providerLng!)),
        padding: const EdgeInsets.all(40),
        maxZoom: 16,
      ),
      interactionOptions: const InteractionOptions(flags: InteractiveFlag.none),
    );
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
              child: FlutterMap(
                options: _mapOptions(),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'br.app.arrow.customer',
                  ),
                  MarkerLayer(
                    markers: [
                      Marker(
                        point: LatLng(latitude!, longitude!),
                        width: 40,
                        height: 40,
                        alignment: Alignment.topCenter,
                        child: const Icon(Icons.location_on, color: Colors.red, size: 36),
                      ),
                      if (_hasProvider)
                        Marker(
                          point: LatLng(providerLat!, providerLng!),
                          width: 40,
                          height: 40,
                          alignment: Alignment.topCenter,
                          child: const Icon(Icons.navigation, color: Color(0xFF2563EB), size: 32),
                        ),
                    ],
                  ),
                  const SimpleAttributionWidget(source: Text('© OpenStreetMap')),
                ],
              ),
            ),
          Padding(
            padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (address.isNotEmpty) Text(address),
                if (_hasProvider) const SizedBox(height: 6),
                if (_hasProvider) Text('Prestador a caminho'.tr, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                const SizedBox(height: 8),
                OutlinedButton.icon(
                  onPressed: _open,
                  icon: const Icon(Icons.directions),
                  label: Text('Como chegar'.tr),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
