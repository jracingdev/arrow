import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/screens/home_shell.dart';
import 'package:provider/screens/login_screen.dart';
import 'package:provider/service/auth_service.dart';
import 'package:provider/themes/app_theme.dart';

class OtpScreen extends StatefulWidget {
  const OtpScreen({super.key});

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final codeController = TextEditingController();
  late final String phoneNumber;
  late final String sessionId;
  late final String displayCode;

  @override
  void initState() {
    super.initState();
    final args = Get.arguments as Map<String, dynamic>? ?? {};
    phoneNumber = args['phoneNumber']?.toString() ?? '';
    sessionId = args['sessionId']?.toString() ?? '';
    displayCode = args['displayCode']?.toString() ?? '';
  }

  @override
  void dispose() {
    codeController.dispose();
    super.dispose();
  }

  Future<void> _verify() async {
    final code = codeController.text.trim();
    if (code.length != 6) {
      ShowToastDialog.showToast('Digite o código de 6 dígitos.');
      return;
    }
    ShowToastDialog.showLoader('Verificando...');
    try {
      await ArrowPhoneOtp.signInWithCode(
        e164: BrazilPhone.toE164(phoneNumber, BrazilPhone.dialCode),
        sessionId: sessionId,
        code: code,
      );
      ShowToastDialog.closeLoader();
      if (await AuthService.admitCurrentUser()) {
        Get.offAll(() => const HomeShell());
      } else {
        Get.offAll(() => const LoginScreen());
      }
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Verificar código')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Enviado para ${BrazilPhone.dialCode} $phoneNumber', style: const TextStyle(color: AppTheme.grey500)),
            const SizedBox(height: 8),
            const Text(
              'O código chegou como notificação neste aparelho. Não é SMS.',
              style: TextStyle(color: AppTheme.grey500, fontSize: 13),
            ),
            if (displayCode.length == 6) ...[
              const SizedBox(height: 16),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(color: AppTheme.grey50, borderRadius: BorderRadius.circular(12)),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Seu código', style: TextStyle(color: AppTheme.grey500, fontSize: 13)),
                    Text(displayCode, style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w700)),
                  ],
                ),
              ),
            ],
            const SizedBox(height: 24),
            TextField(
              controller: codeController,
              keyboardType: TextInputType.number,
              maxLength: 6,
              decoration: const InputDecoration(labelText: 'Código', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            ElevatedButton(onPressed: _verify, child: const Text('Verificar')),
          ],
        ),
      ),
    );
  }
}
