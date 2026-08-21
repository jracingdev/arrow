import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';
import 'package:flutter/material.dart';
import 'package:vendor/app/auth_screen/otp_screen.dart';
import 'package:vendor/constant/show_toast_dialog.dart';
import 'package:vendor/utils/notification_service.dart';

import '../constant/constant.dart';

class PhoneNumberController extends GetxController {
  Rx<TextEditingController> phoneNUmberEditingController = TextEditingController().obs;
  Rx<TextEditingController> countryCodeEditingController = TextEditingController(text: Constant.defaultCountryCode).obs;
  Rx<TextEditingController> countryISOCodeEditingController = TextEditingController(text: Constant.defaultCountryISOCode).obs;

  Future<void> sendCode() async {
    final phoneDigits = BrazilPhone.digitsOnly(phoneNUmberEditingController.value.text);
    final dial = BrazilPhone.normalizeDialCode(countryCodeEditingController.value.text);
    final countryISOCode = BrazilPhone.normalizeIsoCode(countryISOCodeEditingController.value.text);

    if (!BrazilPhone.isValidForDialCode(phoneDigits, dial)) {
      ShowToastDialog.showToast("Please enter a valid Brazilian mobile number".tr);
      return;
    }

    ShowToastDialog.showLoader("please wait...".tr);
    try {
      final sessionId = ArrowPhoneOtp.newSessionId();
      final fcmToken = await NotificationService.getToken();
      final result = await ArrowPhoneOtp.send(
        e164: BrazilPhone.toE164(phoneDigits, dial),
        sessionId: sessionId,
        fcmToken: fcmToken,
      );
      ShowToastDialog.closeLoader();
      Get.to(
        const OtpScreen(),
        arguments: {
          "countryCode": dial,
          "countryISOCode": countryISOCode,
          "phoneNumber": phoneDigits,
          "sessionId": sessionId,
          "displayCode": result.displayCode,
          "fcmDelivered": result.fcmDelivered,
        },
      );
    } catch (error) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(error.toString());
    }
  }
}
