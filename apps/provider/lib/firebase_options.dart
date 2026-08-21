// File generated from official google-services.json (j-arrow).
// ignore_for_file: type=lint
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:firebase_core/firebase_core.dart' show FirebaseOptions;
import 'package:flutter/foundation.dart' show defaultTargetPlatform, kIsWeb, TargetPlatform;

/// Firebase options do app Prestador (`br.app.arrow.provider`).
class DefaultFirebaseOptions {
  static const androidAppId = ArrowFirebaseAndroidAppIds.provider;

  static FirebaseOptions get currentPlatform {
    if (kIsWeb) {
      throw UnsupportedError('Web não configurado para o app Prestador.');
    }
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return android;
      case TargetPlatform.iOS:
        return ios;
      default:
        throw UnsupportedError('Plataforma não configurada para o app Prestador.');
    }
  }

  static const FirebaseOptions android = FirebaseOptions(
    apiKey: 'AIzaSyCkfDofdVDF8BpZ3KC7yuO6D9gznmC4m7E',
    appId: androidAppId,
    messagingSenderId: '661081769489',
    projectId: kFirebaseProjectId,
    databaseURL: 'https://j-arrow-default-rtdb.firebaseio.com',
    storageBucket: 'j-arrow.firebasestorage.app',
  );

  static const FirebaseOptions ios = FirebaseOptions(
    apiKey: 'AIzaSyCkfDofdVDF8BpZ3KC7yuO6D9gznmC4m7E',
    appId: kFirebaseWebAppId,
    messagingSenderId: '661081769489',
    projectId: kFirebaseProjectId,
    databaseURL: 'https://j-arrow-default-rtdb.firebaseio.com',
    storageBucket: 'j-arrow.firebasestorage.app',
    iosBundleId: ArrowIosBundleIds.provider,
  );
}
