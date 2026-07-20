import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:get/get_utils/src/extensions/internacionalization.dart';

class ShowToastDialog {
  static void showToast(String? message, {EasyLoadingToastPosition position = EasyLoadingToastPosition.top}) {
    if (message == null || message.isEmpty) return;
    if (EasyLoading.isShow) {
      EasyLoading.dismiss();
    }
    final text = _looksTechnical(message) ? message : message.tr;
    EasyLoading.showToast(text, toastPosition: position, duration: const Duration(seconds: 6));
  }

  static bool _looksTechnical(String message) {
    return message.contains('SHA-1') ||
        message.contains('DEVELOPER_ERROR') ||
        message.contains('ApiException') ||
        message.contains('Firebase j-arrow') ||
        message.contains('Cadastre no Firebase');
  }

  static void showLoader(String message) {
    EasyLoading.show(status: message, maskType: EasyLoadingMaskType.black, dismissOnTap: false);
  }

  static void closeLoader() {
    EasyLoading.dismiss();
  }
}
