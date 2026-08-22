import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';

class DispatchRingtone {
  DispatchRingtone._();

  static final FlutterRingtonePlayer _player = FlutterRingtonePlayer();
  static bool _playing = false;

  static Future<void> start() async {
    if (_playing) return;
    _playing = true;
    try {
      await _player.play(
        android: AndroidSounds.ringtone,
        ios: IosSounds.electronic,
        looping: true,
        volume: 1,
        asAlarm: true,
      );
    } catch (_) {
      _playing = false;
    }
  }

  static Future<void> stop() async {
    if (!_playing) {
      try {
        await _player.stop();
      } catch (_) {}
      return;
    }
    _playing = false;
    try {
      await _player.stop();
    } catch (_) {}
  }
}
