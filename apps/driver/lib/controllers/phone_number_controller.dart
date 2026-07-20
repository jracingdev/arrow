import 'package:arrow_shared/brazil_phone.dart';
import 'package:driver/app/auth_screen/otp_screen.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

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

    ShowToastDialog.showLoader("Please wait".tr);
    await FirebaseAuth.instance
        .verifyPhoneNumber(
            phoneNumber: '$dial$phoneDigits',
            verificationCompleted: (PhoneAuthCredential credential) {},
            verificationFailed: (FirebaseAuthException e) {
              debugPrint("FirebaseAuthException--->${e.message}");
              ShowToastDialog.closeLoader();
              if (e.code == 'invalid-phone-number') {
                ShowToastDialog.showToast("invalid_phone_number".tr);
              } else {
                ShowToastDialog.showToast(e.message);
              }
            },
            codeSent: (String verificationId, int? resendToken) {
              ShowToastDialog.closeLoader();
              Get.to(const OtpScreen(), arguments: {
                "countryCode": dial,
                "countryISOCode": countryISOCode,
                "phoneNumber": phoneDigits,
                "verificationId": verificationId,
              });
            },
            codeAutoRetrievalTimeout: (String verificationId) {})
        .catchError((error) {
      debugPrint("catchError--->$error");
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("multiple_time_request".tr);
    });
  }
}
