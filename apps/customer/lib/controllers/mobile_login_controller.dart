import 'package:customer/themes/show_toast_dialog.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/brazil_phone.dart';

import '../constant/constant.dart';
import '../screen_ui/auth_screens/otp_verification_screen.dart';
import '../utils/notification_service.dart';

class MobileLoginController extends GetxController {
  final Rx<TextEditingController> mobileController = TextEditingController().obs;
  final Rx<TextEditingController> countryCodeController = TextEditingController(text: Constant.defaultCountryCode).obs;
  final Rx<TextEditingController> countryISOCodeController = TextEditingController(text: Constant.defaultCountryISOCode).obs;

  /// Send OTP to this device (FCM + código na tela). Não usa SMS.
  Future<void> sendOtp() async {
    final mobileDigits = BrazilPhone.digitsOnly(mobileController.value.text);
    final countryCode = BrazilPhone.normalizeDialCode(countryCodeController.value.text.trim());
    final countryISOCode = BrazilPhone.normalizeIsoCode(countryISOCodeController.value.text.trim());

    if (!BrazilPhone.isValidForDialCode(mobileDigits, countryCode)) {
      ShowToastDialog.showToast("Please enter a valid Brazilian mobile number".tr);
      return;
    }

    try {
      ShowToastDialog.showLoader("Sending OTP...".tr);
      final sessionId = ArrowPhoneOtp.newSessionId();
      final fcmToken = await NotificationService.getToken();
      final result = await ArrowPhoneOtp.send(
        e164: BrazilPhone.toE164(mobileDigits, countryCode),
        sessionId: sessionId,
        fcmToken: fcmToken,
      );
      ShowToastDialog.closeLoader();
      Get.to(() => const OtpVerificationScreen(), arguments: {
        'countryCode': countryCode,
        'countryISOCode': countryISOCode,
        'phoneNumber': mobileDigits,
        'sessionId': sessionId,
        'displayCode': result.displayCode,
        'fcmDelivered': result.fcmDelivered,
      });
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  void onClose() {
    mobileController.value.dispose();
    countryCodeController.value.dispose();
    countryISOCodeController.value.dispose();
    super.onClose();
  }
}
