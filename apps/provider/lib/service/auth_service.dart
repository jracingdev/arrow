import 'package:arrow_shared/arrow_production_config.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/service/notification_service.dart';

class AuthService {
  AuthService._();

  static Future<bool> admitCurrentUser() async {
    final uid = FirebaseAuth.instance.currentUser?.uid;
    if (uid == null || uid.isEmpty) return false;

    final user = await FireStoreUtils.getUserProfile(uid);
    if (user == null || user.role != Constant.userRoleProvider) {
      await FirebaseAuth.instance.signOut();
      ShowToastDialog.showToast('Esta conta não é de prestador. Use um usuário com role provider.');
      return false;
    }
    if (user.active != true) {
      await FirebaseAuth.instance.signOut();
      ShowToastDialog.showToast('Este usuário está desativado. Fale com o administrador.');
      return false;
    }

    try {
      user.fcmToken = await NotificationService.getToken();
    } catch (_) {}
    user.appIdentifier = ArrowAndroidPackages.provider;
    await FireStoreUtils.updateUser(user);
    Constant.userModel = user;
    return true;
  }
}
