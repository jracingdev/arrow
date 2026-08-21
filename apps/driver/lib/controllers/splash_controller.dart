import 'dart:async';
import 'dart:developer';

import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:driver/app/auth_screen/login_screen.dart';
import 'package:driver/app/maintenance_mode_screen/maintenance_mode_screen.dart';
import 'package:driver/app/on_boarding_screen.dart';
import 'package:driver/app/owner_screen/owner_dashboard_screen.dart';
import 'package:driver/constant/constant.dart';
import 'package:driver/controllers/signup_controller.dart';
import 'package:driver/models/user_model.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/notification_service.dart';
import 'package:driver/utils/preferences.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    Timer(const Duration(seconds: 3), () => redirectScreen());
    super.onInit();
  }

  Future<void> redirectScreen() async {
    try {
      if (await FireStoreUtils.isMaintenanceMode() == true) {
        Get.offAll(() => MaintenanceModeScreen());
        return;
      }
      if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
        Get.offAll(const OnboardingScreen());
        return;
      }
      bool isLogin = await FireStoreUtils.isLogin();
      final auth = ArrowSecureAuth.forApp(ArrowAndroidPackages.driver);
      final gate = await auth.shouldAttemptLogin(hasFirebaseSession: isLogin);
      if (gate == ArrowAuthGate.sessionLock) {
        Get.offAll(() => ArrowBiometricLockPage(
              auth: auth,
              onUnlocked: () => _admitLoggedInUser(),
              onUsePassword: () => Get.offAll(const LoginScreen()),
            ));
        return;
      }
      if (isLogin == true) {
        await _admitLoggedInUser();
      } else {
        await FirebaseAuth.instance.signOut();
        Get.offAll(const LoginScreen());
      }
    } catch (e, st) {
      log('splash redirect failed: $e\n$st');
      Get.offAll(const LoginScreen());
    }
  }

  Future<void> _admitLoggedInUser() async {
    await FireStoreUtils.getUserProfile(FireStoreUtils.getCurrentUid()).then((value) async {
      if (value != null) {
        UserModel userModel = value;
        log(userModel.toJson().toString());
        if (userModel.role == Constant.userRoleDriver) {
          if (userModel.active == true) {
            try {
              userModel.fcmToken = await NotificationService.getToken();
            } catch (_) {}
            await FireStoreUtils.updateUser(userModel);
            if (userModel.isOwner == true) {
              Get.offAll(OwnerDashboardScreen());
            } else {
              SignupController.navigateByUserModel(userModel);
            }
          } else {
            await FirebaseAuth.instance.signOut();
            Get.offAll(const LoginScreen());
          }
        } else {
          await FirebaseAuth.instance.signOut();
          Get.offAll(const LoginScreen());
        }
      } else {
        await FirebaseAuth.instance.signOut();
        Get.offAll(const LoginScreen());
      }
    });
  }
}
