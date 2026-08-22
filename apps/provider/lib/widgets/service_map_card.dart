import 'package:arrow_shared/geo_distance.dart';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/utils/service_navigation.dart';

class ServiceMapCard extends StatelessWidget {
  const ServiceMapCard({
    super.key,
    required this.title,
    required this.address,
    this.latitude,
    this.longitude,
    this.navigateLabel = 'Como chegar',
  });

  final String title;
  final String address;
  final double? latitude;
  final double? longitude;
  final String navigateLabel;

  bool get _hasCoords => GeoDistance.isValid(latitude, longitude);

  Future<void> _open() {
    return ServiceNavigation.open(
      name: title,
      latitude: _hasCoords ? latitude : null,
      longitude: _hasCoords ? longitude : null,
      address: address,
    );
  }

  @override
  Widget build(BuildContext context) {
    if (!_hasCoords && address.isEmpty) return const SizedBox.shrink();
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: AppTheme.grey200),
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          if (_hasCoords)
            SizedBox(
              height: 180,
              child: FlutterMap(
                options: MapOptions(
                  initialCenter: LatLng(latitude!, longitude!),
                  initialZoom: 15,
                  interactionOptions: const InteractionOptions(flags: InteractiveFlag.none),
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'br.app.arrow.provider',
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
                    ],
                  ),
                  const SimpleAttributionWidget(source: Text('© OpenStreetMap')),
                ],
              ),
            )
          else
            const Padding(
              padding: EdgeInsets.fromLTRB(16, 16, 16, 0),
              child: Row(
                children: [
                  Icon(Icons.place_outlined, color: AppTheme.primary),
                  SizedBox(width: 8),
                  Expanded(child: Text('Local do serviço', style: TextStyle(fontWeight: FontWeight.w600))),
                ],
              ),
            ),
          if (address.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
              child: Text(address, style: const TextStyle(color: AppTheme.grey700)),
            ),
          Padding(
            padding: const EdgeInsets.all(12),
            child: OutlinedButton.icon(
              onPressed: _open,
              icon: const Icon(Icons.directions),
              label: Text(navigateLabel),
            ),
          ),
        ],
      ),
    );
  }
}
