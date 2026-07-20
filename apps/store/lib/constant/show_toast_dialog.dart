import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:get/get_utils/src/extensions/internacionalization.dart';

class ShowToastDialog {
  static void showToast(String? message, {EasyLoadingToastPosition position = EasyLoadingToastPosition.top}) {
    if (message == null || message.isEmpty) return;
    if (EasyLoading.isShow) {
      EasyLoading.dismiss();
    }
    // Do not .tr technical Firebase/SHA messages (would hide the fingerprint).
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

  static void showToastDuration(String? message, {EasyLoadingToastPosition position = EasyLoadingToastPosition.top, required Duration duration}) {
    if (message == null || message.isEmpty) return;
    if (EasyLoading.isShow) {
      EasyLoading.dismiss();
    }
    final text = _looksTechnical(message) ? message : message.tr;
    EasyLoading.showToast(text, toastPosition: position, duration: duration);
  }

  static void showLoader(String message) {
    EasyLoading.show(status: message.tr);
  }

  static void closeLoader() {
    EasyLoading.dismiss();
  }
}
