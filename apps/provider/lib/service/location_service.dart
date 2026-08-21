import 'dart:async';

import 'package:flutter/widgets.dart';
import 'package:geolocator/geolocator.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/service/fire_store_utils.dart';

/// Atualiza a localização do prestador só com o app em primeiro plano (v1).
class ProviderLocationService with WidgetsBindingObserver {
  ProviderLocationService._();
  static final ProviderLocationService instance = ProviderLocationService._();

  StreamSubscription<Position>? _sub;
  Timer? _timer;
  DateTime? _lastWrite;
  bool _started = false;

  static void start() => instance._start();

  static void stop() => instance._stop();

  void _start() {
    if (_started) return;
    _started = true;
    WidgetsBinding.instance.addObserver(this);
    _listen();
  }

  void _stop() {
    _started = false;
    WidgetsBinding.instance.removeObserver(this);
    _sub?.cancel();
    _timer?.cancel();
    _sub = null;
    _timer = null;
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _listen();
    } else if (state == AppLifecycleState.paused || state == AppLifecycleState.inactive || state == AppLifecycleState.hidden) {
      _sub?.cancel();
      _timer?.cancel();
      _sub = null;
      _timer = null;
    }
  }

  Future<void> _listen() async {
    _sub?.cancel();
    _timer?.cancel();
    final enabled = await Geolocator.isLocationServiceEnabled();
    if (!enabled) return;
    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }
    if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) return;

    _sub = Geolocator.getPositionStream(
      locationSettings: const LocationSettings(accuracy: LocationAccuracy.high, distanceFilter: 40),
    ).listen((pos) => _write(pos.latitude, pos.longitude));

    _timer = Timer.periodic(const Duration(seconds: 45), (_) async {
      try {
        final pos = await Geolocator.getCurrentPosition(locationSettings: const LocationSettings(accuracy: LocationAccuracy.high));
        await _write(pos.latitude, pos.longitude);
      } catch (_) {}
    });

    try {
      final pos = await Geolocator.getCurrentPosition(locationSettings: const LocationSettings(accuracy: LocationAccuracy.high));
      await _write(pos.latitude, pos.longitude);
    } catch (_) {}
  }

  Future<void> _write(double lat, double lng) async {
    final uid = FireStoreUtils.getCurrentUid();
    if (uid.isEmpty) return;
    final now = DateTime.now();
    if (_lastWrite != null && now.difference(_lastWrite!) < const Duration(seconds: 15)) return;
    _lastWrite = now;
    Constant.userModel?.latitude = lat;
    Constant.userModel?.longitude = lng;
    await FireStoreUtils.updateUserLocation(uid, lat, lng);
  }
}
