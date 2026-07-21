import 'package:customer/themes/show_toast_dialog.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:arrow_shared/arrow_phone_auth.dart';
import 'package:arrow_shared/brazil_phone.dart';

import '../constant/constant.dart';
import '../screen_ui/auth_screens/otp_verification_screen.dart';

class MobileLoginController extends GetxController {
  final Rx<TextEditingController> mobileController = TextEditingController().obs;
  final Rx<TextEditingController> countryCodeController = TextEditingController(text: Constant.defaultCountryCode).obs;
  final Rx<TextEditingController> countryISOCodeController = TextEditingController(text: Constant.defaultCountryISOCode).obs;

  final FirebaseAuth _auth = FirebaseAuth.instance;

  /// Send OTP to the entered phone number
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

      await _auth.verifyPhoneNumber(
        phoneNumber: '$countryCode$mobileDigits',
        verificationCompleted: (PhoneAuthCredential credential) {
          // Optionally handle auto-verification
        },
        verificationFailed: (FirebaseAuthException e) {
          ShowToastDialog.closeLoader();
          ShowToastDialog.showToast(ArrowPhoneAuth.toastFor(e));
        },
        codeSent: (String verificationId, int? resendToken) {
          ShowToastDialog.closeLoader();
          Get.to(() => const OtpVerificationScreen(), arguments: {
            'countryCode': countryCode,
            'countryISOCode': countryISOCode,
            'phoneNumber': mobileDigits,
            'verificationId': verificationId,
          });
        },
        codeAutoRetrievalTimeout: (String verificationId) {
          ShowToastDialog.closeLoader();
          ShowToastDialog.showToast("OTP timed out. Please try again.".tr);
        },
      );
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(ArrowPhoneAuth.toastForError(e));
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
