import 'package:flutter_easyloading/flutter_easyloading.dart';

class ShowToastDialog {
  static void showToast(String? message, {EasyLoadingToastPosition position = EasyLoadingToastPosition.top}) {
    if (message == null || message.isEmpty) return;
    if (EasyLoading.isShow) {
      EasyLoading.dismiss();
    }
    EasyLoading.showToast(
      message,
      toastPosition: position,
      duration: Duration(seconds: _looksTechnical(message) ? 14 : 6),
    );
  }

  static bool _looksTechnical(String message) {
    return message.contains('SHA-1') ||
        message.contains('SHA-256') ||
        message.contains('invalid-cert-hash') ||
        message.contains('DEVELOPER_ERROR') ||
        message.contains('ApiException') ||
        message.contains('Firebase j-arrow') ||
        message.contains('Adicionar impressao digital');
  }

  static void showLoader(String message) {
    EasyLoading.show(status: message);
  }

  static void closeLoader() {
    EasyLoading.dismiss();
  }
}
