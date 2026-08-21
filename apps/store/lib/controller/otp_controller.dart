import 'package:arrow_shared/arrow_phone_otp.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:get/get.dart';
import 'package:pin_code_fields/pin_code_fields.dart';
import 'package:vendor/constant/show_toast_dialog.dart';
import 'package:vendor/utils/notification_service.dart';

class OtpController extends GetxController {
  Rx<PinInputController> otpController = PinInputController().obs;

  RxString countryCode = "".obs;
  RxString countryISOCode = "".obs;
  RxString phoneNumber = "".obs;
  RxString sessionId = "".obs;
  RxString displayCode = "".obs;
  RxBool fcmDelivered = false.obs;
  RxBool isLoading = true.obs;

  String get e164 => BrazilPhone.toE164(phoneNumber.value, countryCode.value);

  @override
  void onInit() {
    getArgument();
    super.onInit();
  }

  Future<void> getArgument() async {
    dynamic argumentData = Get.arguments;
    if (argumentData != null) {
      countryCode.value = argumentData['countryCode'] ?? "";
      countryISOCode.value = argumentData['countryISOCode'] ?? "";
      phoneNumber.value = argumentData['phoneNumber'] ?? "";
      sessionId.value = argumentData['sessionId'] ?? "";
      displayCode.value = argumentData['displayCode'] ?? "";
      fcmDelivered.value = argumentData['fcmDelivered'] == true;
    }
    isLoading.value = false;
    update();
  }

  Future<bool> sendOTP() async {
    try {
      final nextSession = ArrowPhoneOtp.newSessionId();
      final fcmToken = await NotificationService.getToken();
      final result = await ArrowPhoneOtp.send(
        e164: e164,
        sessionId: nextSession,
        fcmToken: fcmToken,
      );
      sessionId.value = nextSession;
      displayCode.value = result.displayCode;
      fcmDelivered.value = result.fcmDelivered;
      ShowToastDialog.showToast("OTP sent".tr);
    } catch (e) {
      ShowToastDialog.showToast(e.toString());
    }
    return true;
  }
}
