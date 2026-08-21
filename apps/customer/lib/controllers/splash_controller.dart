import 'dart:async';
import 'dart:convert';
import 'dart:developer';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/models/language_model.dart';
import 'package:customer/models/user_model.dart';
import 'package:customer/screen_ui/maintenance_mode_screen/maintenance_mode_screen.dart';
import 'package:customer/screen_ui/service_home_screen/service_list_screen.dart';
import 'package:customer/service/localization_service.dart';
import 'package:customer/utils/notification_service.dart';
import 'package:customer/utils/preferences.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:get/get.dart';
import '../screen_ui/auth_screens/login_screen.dart';
import '../screen_ui/location_enable_screens/location_permission_screen.dart';
import '../screen_ui/on_boarding_screen/on_boarding_screen.dart';
import '../service/fire_store_utils.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    Timer(const Duration(seconds: 3), () => redirectScreen());
    super.onInit();
  }

  Future<void> getLanguage() async {
    final jsonString = Preferences.getString(Preferences.languageCodeKey);
    if (jsonString != '' && jsonString.isNotEmpty) {
      final Map<String, dynamic> jsonData = jsonDecode(jsonString);
      final model = LanguageModel.fromJson(jsonData);
      LocalizationService().changeLocale(model.slug!);
    } else {
      LocalizationService().changeLocale('pt_br');
    }
  }

  Future<void> redirectScreen() async {
    try {
      getLanguage();
      if (await FireStoreUtils.isMaintenanceMode() == true) {
        Get.offAll(() => MaintenanceModeScreen());
        return;
      }
      if (Preferences.getBoolean(Preferences.isFinishOnBoardingKey) == false) {
        Get.offAll(const OnboardingScreen());
        return;
      }
      bool isLogin = await FireStoreUtils.isLogin();
      final auth = ArrowSecureAuth.forApp(ArrowAndroidPackages.customer);
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
        if (userModel.role == Constant.userRoleCustomer) {
          if (userModel.active == true) {
            try {
              userModel.fcmToken = await NotificationService.getToken();
            } catch (_) {}
            await FireStoreUtils.updateUser(userModel);
            if (userModel.shippingAddress != null && userModel.shippingAddress!.isNotEmpty) {
              if (userModel.shippingAddress!.where((element) => element.isDefault == true).isNotEmpty) {
                Constant.selectedLocation = userModel.shippingAddress!.where((element) => element.isDefault == true).single;
              } else {
                Constant.selectedLocation = userModel.shippingAddress!.first;
              }
              Get.offAll(const ServiceListScreen());
            } else {
              Get.offAll(const LocationPermissionScreen());
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
