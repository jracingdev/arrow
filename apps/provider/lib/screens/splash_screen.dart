import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/screens/home_shell.dart';
import 'package:provider/screens/login_screen.dart';
import 'package:provider/service/auth_service.dart';
import 'package:provider/themes/app_theme.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _boot();
  }

  Future<void> _boot() async {
    await Future<void>.delayed(const Duration(milliseconds: 400));
    final user = FirebaseAuth.instance.currentUser;
    final auth = ArrowSecureAuth.forApp(ArrowAndroidPackages.provider);
    final hasSession = user != null;
    final gate = await auth.shouldAttemptLogin(hasFirebaseSession: hasSession);
    if (gate == ArrowAuthGate.sessionLock) {
      Get.offAll(() => ArrowBiometricLockPage(
            auth: auth,
            onUnlocked: () => _admitOrLogin(),
            onUsePassword: () => Get.offAll(() => const LoginScreen()),
          ));
      return;
    }
    await _admitOrLogin();
  }

  Future<void> _admitOrLogin() async {
    final user = FirebaseAuth.instance.currentUser;
    if (user == null) {
      Get.offAll(() => const LoginScreen());
      return;
    }
    final ok = await AuthService.admitCurrentUser();
    if (ok) {
      Get.offAll(() => const HomeShell());
    } else {
      Get.offAll(() => const LoginScreen());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF111827),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.asset('assets/provider-logo.png', width: 280),
            const SizedBox(height: 24),
            const CircularProgressIndicator(color: AppTheme.primary),
          ],
        ),
      ),
    );
  }
}
