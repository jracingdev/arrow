import 'package:flutter_easyloading/flutter_easyloading.dart';

class ShowToastDialog {
  /// Show a toast message with customizable position.
  static void showToast(
    String? message, {
    EasyLoadingToastPosition position = EasyLoadingToastPosition.top,
  }) {
    if (message == null || message.isEmpty) return;
    // Dismiss any loader first — otherwise toasts are often invisible.
    if (EasyLoading.isShow) {
      EasyLoading.dismiss();
    }
    EasyLoading.showToast(
      message,
      toastPosition: position,
      duration: Duration(seconds: _isShaConfig(message) ? 14 : 5),
    );
  }

  static bool _isShaConfig(String message) {
    return message.contains('SHA-1') ||
        message.contains('invalid-cert-hash') ||
        message.contains('DEVELOPER_ERROR') ||
        message.contains('ApiException') ||
        message.contains('Adicionar impressao digital');
  }

  /// Show a loading indicator with a status message.
  static void showLoader(String message) {
    EasyLoading.show(
      status: message,
      dismissOnTap: false,
      maskType: EasyLoadingMaskType.clear,
    );
  }

  /// Dismiss any active loading indicator.
  static void closeLoader() {
    EasyLoading.dismiss();
  }
}
