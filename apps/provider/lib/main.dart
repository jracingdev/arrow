import 'package:firebase_app_check/firebase_app_check.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:get/get.dart';
import 'package:provider/firebase_options.dart';
import 'package:provider/screens/splash_screen.dart';
import 'package:provider/service/notification_service.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/widgets/dispatch_incoming_overlay.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  late final FirebaseApp firebaseApp;
  try {
    firebaseApp = await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  } catch (_) {
    firebaseApp = Firebase.app();
  }
  debugPrint('Firebase app: ${firebaseApp.name}');

  try {
    await FirebaseAppCheck.instance.activate(
      androidProvider: AndroidProvider.playIntegrity,
      appleProvider: AppleProvider.appAttest,
    );
  } catch (_) {}

  EasyLoading.instance
    ..indicatorType = EasyLoadingIndicatorType.fadingCircle
    ..loadingStyle = EasyLoadingStyle.custom
    ..backgroundColor = AppTheme.grey900
    ..indicatorColor = Colors.white
    ..textColor = Colors.white
    ..userInteractions = false
    ..dismissOnTap = false;

  runApp(const ProviderApp());
}

class ProviderApp extends StatefulWidget {
  const ProviderApp({super.key});

  @override
  State<ProviderApp> createState() => _ProviderAppState();
}

class _ProviderAppState extends State<ProviderApp> {
  @override
  void initState() {
    super.initState();
    NotificationService().initInfo();
  }

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'Arrow Prestador',
      debugShowCheckedModeBanner: false,
      locale: const Locale('pt', 'BR'),
      fallbackLocale: const Locale('pt', 'BR'),
      theme: AppTheme.light(),
      builder: (context, child) => DispatchIncomingScope(child: EasyLoading.init()(context, child)),
      home: const SplashScreen(),
    );
  }
}
