import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/screens/otp_screen.dart';
import 'package:provider/service/notification_service.dart';
import 'package:provider/themes/app_theme.dart';

class PhoneScreen extends StatefulWidget {
  const PhoneScreen({super.key});

  @override
  State<PhoneScreen> createState() => _PhoneScreenState();
}

class _PhoneScreenState extends State<PhoneScreen> {
  final phoneController = TextEditingController();

  @override
  void dispose() {
    phoneController.dispose();
    super.dispose();
  }

  Future<void> _send() async {
    final digits = BrazilPhone.digitsOnly(phoneController.text);
    if (!BrazilPhone.isValid(digits)) {
      ShowToastDialog.showToast('Informe um celular brasileiro válido.');
      return;
    }
    ShowToastDialog.showLoader('Enviando código...');
    try {
      final sessionId = ArrowPhoneOtp.newSessionId();
      final fcmToken = await NotificationService.getToken();
      final result = await ArrowPhoneOtp.send(
        e164: BrazilPhone.toE164(digits, BrazilPhone.dialCode),
        sessionId: sessionId,
        fcmToken: fcmToken,
      );
      ShowToastDialog.closeLoader();
      Get.to(
        () => const OtpScreen(),
        arguments: {
          'phoneNumber': digits,
          'sessionId': sessionId,
          'displayCode': result.displayCode,
        },
      );
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Telefone')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'O código chega neste aparelho (notificação), não por SMS.',
              style: TextStyle(color: AppTheme.grey500),
            ),
            const SizedBox(height: 20),
            TextField(
              controller: phoneController,
              keyboardType: TextInputType.phone,
              decoration: InputDecoration(
                labelText: 'Celular',
                hintText: BrazilPhone.hint,
                prefixText: '${BrazilPhone.dialCode} ',
                border: const OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 20),
            ElevatedButton(onPressed: _send, child: const Text('Enviar código')),
          ],
        ),
      ),
    );
  }
}
