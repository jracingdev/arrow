import 'package:customer/constant/constant.dart';
import 'package:customer/models/user_model.dart';
import 'package:customer/widget/place_picker/selected_location_model.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get_utils/src/extensions/internacionalization.dart';
import 'package:map_launcher/map_launcher.dart';
import '../themes/show_toast_dialog.dart';
import 'package:geocoding/geocoding.dart';
import 'package:location/location.dart' as loc;

class Utils {
  static const LocationSettings locationSettings = LocationSettings(
    accuracy: LocationAccuracy.high,
    distanceFilter: 0,
    timeLimit: Duration(seconds: 15),
  );

  /// Ensures GPS is on and permission is granted. Returns false if the user
  /// refuses location services or permission.
  static Future<bool> ensureLocationReady() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      await loc.Location().requestService();
      serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        return false;
      }
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }
    if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
      return false;
    }
    return true;
  }

  static Future<Position?> getCurrentLocation() async {
    final ready = await ensureLocationReady();
    if (!ready) {
      return null;
    }

    try {
      return await Geolocator.getCurrentPosition(locationSettings: locationSettings);
    } catch (_) {
      // Indoor / slow GPS: last known is better than a fake overseas fallback.
      return await Geolocator.getLastKnownPosition();
    }
  }

  /// Builds a [ShippingAddress] from the device GPS. Returns null on failure
  /// (never invents coordinates from another country).
  static Future<ShippingAddress?> buildAddressFromCurrentPosition({String addressAs = "Home"}) async {
    final position = await getCurrentLocation();
    if (position == null) {
      return null;
    }

    String locality = "${position.latitude.toStringAsFixed(5)}, ${position.longitude.toStringAsFixed(5)}";
    try {
      final placeMarks = await placemarkFromCoordinates(position.latitude, position.longitude)
          .timeout(const Duration(seconds: 10));
      if (placeMarks.isNotEmpty) {
        final placeMark = placeMarks.first;
        locality =
            "${placeMark.name}, ${placeMark.subLocality}, ${placeMark.locality}, ${placeMark.administrativeArea}, ${placeMark.postalCode}, ${placeMark.country}";
      }
    } catch (_) {
      // Keep coordinate fallback locality (geocoder can hang or fail offline).
    }

    return ShippingAddress(
      addressAs: addressAs,
      locality: locality,
      location: UserLocation(latitude: position.latitude, longitude: position.longitude),
    );
  }

  static Future<String> getAddressFromCoordinates(double lat, double lng) async {
    try {
      List<Placemark> placemarks = await placemarkFromCoordinates(lat, lng);
      if (placemarks.isNotEmpty) {
        Placemark place = placemarks.first;
        String address = "${place.name ?? ''}, ${place.subLocality ?? ''}, ${place.locality ?? ''}, ${place.administrativeArea ?? ''}, ${place.country ?? ''}";
        return address;
      }
      return "Unknown location";
    } catch (e) {
      return "Unknown location";
    }
  }

  static Future<void> redirectMap({required String name, required double latitude, required double longLatitude}) async {
    if (Constant.mapType == "google") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.google);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.google, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("Google map is not installed".tr);
      }
    } else if (Constant.mapType == "googleGo") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.googleGo);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.googleGo, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("Google Go map is not installed".tr);
      }
    } else if (Constant.mapType == "waze") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.waze);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.waze, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("Waze is not installed".tr);
      }
    } else if (Constant.mapType == "mapswithme") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.mapswithme);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.mapswithme, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("Mapswithme is not installed".tr);
      }
    } else if (Constant.mapType == "yandexNavi") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.yandexNavi);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.yandexNavi, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("YandexNavi is not installed".tr);
      }
    } else if (Constant.mapType == "yandexMaps") {
      bool? isAvailable = await MapLauncher.isMapAvailable(MapType.yandexMaps);
      if (isAvailable == true) {
        await MapLauncher.showDirections(mapType: MapType.yandexMaps, directionsMode: DirectionsMode.driving, destinationTitle: name, destination: Coords(latitude, longLatitude));
      } else {
        ShowToastDialog.showToast("yandexMaps map is not installed".tr);
      }
    }
  }

  static String formatAddress({required SelectedLocationModel selectedLocation}) {
    List<String> parts = [];

    if (selectedLocation.address!.name != null && selectedLocation.address!.name!.isNotEmpty) parts.add(selectedLocation.address!.name!);
    if (selectedLocation.address!.subThoroughfare != null && selectedLocation.address!.subThoroughfare!.isNotEmpty) parts.add(selectedLocation.address!.subThoroughfare!);
    if (selectedLocation.address!.thoroughfare != null && selectedLocation.address!.thoroughfare!.isNotEmpty) parts.add(selectedLocation.address!.thoroughfare!);
    if (selectedLocation.address!.subLocality != null && selectedLocation.address!.subLocality!.isNotEmpty) parts.add(selectedLocation.address!.subLocality!);
    if (selectedLocation.address!.locality != null && selectedLocation.address!.locality!.isNotEmpty) parts.add(selectedLocation.address!.locality!);
    if (selectedLocation.address!.subAdministrativeArea != null && selectedLocation.address!.subAdministrativeArea!.isNotEmpty) {
      parts.add(selectedLocation.address!.subAdministrativeArea!);
    }
    if (selectedLocation.address!.administrativeArea != null && selectedLocation.address!.administrativeArea!.isNotEmpty) parts.add(selectedLocation.address!.administrativeArea!);
    if (selectedLocation.address!.postalCode != null && selectedLocation.address!.postalCode!.isNotEmpty) parts.add(selectedLocation.address!.postalCode!);
    if (selectedLocation.address!.country != null && selectedLocation.address!.country!.isNotEmpty) parts.add(selectedLocation.address!.country!);
    if (selectedLocation.address!.isoCountryCode != null && selectedLocation.address!.isoCountryCode!.isNotEmpty) parts.add(selectedLocation.address!.isoCountryCode!);

    return parts.join(', ');
  }
}
