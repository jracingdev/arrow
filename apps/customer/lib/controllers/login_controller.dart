import 'dart:convert';
import 'package:arrow_shared/arrow_auth_errors.dart';
import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:customer/screen_ui/location_enable_screens/location_permission_screen.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';
import '../constant/constant.dart';
import '../models/user_model.dart';
import '../screen_ui/auth_screens/login_screen.dart';
import '../screen_ui/auth_screens/sign_up_screen.dart';
import '../screen_ui/service_home_screen/service_list_screen.dart';
import '../service/fire_store_utils.dart';
import '../themes/show_toast_dialog.dart';
import '../utils/notification_service.dart';
import 'package:crypto/crypto.dart';

class LoginController extends GetxController {
  Rx<TextEditingController> emailController = TextEditingController().obs;
  Rx<TextEditingController> passwordController = TextEditingController().obs;

  /// Focus nodes
  final FocusNode emailFocusNode = FocusNode();
  final FocusNode passwordFocusNode = FocusNode();

  /// Loading indicator
  final RxBool isLoading = false.obs;

  RxBool passwordVisible = true.obs;
  RxBool rememberMe = true.obs;
  RxBool showBiometricLogin = false.obs;
  final ArrowSecureAuth arrowAuth = ArrowSecureAuth.forApp(ArrowAndroidPackages.customer);

  @override
  void onInit() {
    super.onInit();
    _restoreRemembered();
  }

  Future<void> _restoreRemembered() async {
    try {
      rememberMe.value = await arrowAuth.isRememberMe();
      final email = await arrowAuth.prefillEmail();
      final password = await arrowAuth.prefillPassword();
      if (email != null && email.isNotEmpty) emailController.value.text = email;
      if (password != null && password.isNotEmpty) passwordController.value.text = password;
      showBiometricLogin.value = await arrowAuth.isBiometricsEnabled() && await arrowAuth.hasPasswordSecret();
      final gate = await arrowAuth.shouldAttemptLogin(
        hasFirebaseSession: FirebaseAuth.instance.currentUser != null,
      );
      if (gate == ArrowAuthGate.credentialLogin) {
        await loginWithBiometrics();
      }
    } catch (_) {}
  }

  Future<void> setRememberMe(bool value) async {
    rememberMe.value = value;
    await arrowAuth.setRememberMe(value);
    if (!value) passwordController.value.clear();
  }

  Future<void> forgetDevice() async {
    await arrowAuth.forgetDevice();
    rememberMe.value = false;
    showBiometricLogin.value = false;
    emailController.value.clear();
    passwordController.value.clear();
  }

  Future<void> loginWithBiometrics() async {
    final ok = await arrowAuth.authenticate();
    if (!ok) return;
    final creds = await arrowAuth.passwordCredentials();
    if (creds == null) return;
    emailController.value.text = creds.email;
    passwordController.value.text = creds.password;
    await loginWithEmail();
  }

  Future<void> loginWithEmail() async {
    final email = emailController.value.text.trim();
    final password = passwordController.value.text.trim();
    if (email.isEmpty || !email.contains('@')) {
      ShowToastDialog.showToast("Please enter a valid email address".tr);
      return;
    }

    if (password.isEmpty) {
      ShowToastDialog.showToast("Please enter your password".tr);
      return;
    }

    try {
      isLoading.value = true;
      ShowToastDialog.showLoader("Logging in...".tr);

      final credential = await FirebaseAuth.instance.signInWithEmailAndPassword(email: email, password: password);

      final userModel = await FireStoreUtils.getUserProfile(credential.user!.uid);

      if (userModel != null && userModel.role == Constant.userRoleCustomer) {
        if (userModel.active == true) {
          try {
            userModel.fcmToken = await NotificationService.getToken();
          } catch (_) {}
          await FireStoreUtils.updateUser(userModel);
          await ArrowSecureAuthUi.afterPasswordLogin(
            Get.context,
            arrowAuth,
            email: email,
            password: password,
            rememberMe: rememberMe.value,
          );

          if (userModel.shippingAddress != null && userModel.shippingAddress!.isNotEmpty) {
            final defaultAddress = userModel.shippingAddress!.firstWhere((e) => e.isDefault == true, orElse: () => userModel.shippingAddress!.first);

            Constant.selectedLocation = defaultAddress;

            Get.offAll(() => const ServiceListScreen());
          } else {
            Get.offAll(() => const LocationPermissionScreen());
          }
        } else {
          await FirebaseAuth.instance.signOut();
          ShowToastDialog.showToast("This user is disabled. Please contact admin.".tr);
          Get.offAll(() => const LoginScreen());
        }
      } else {
        await FirebaseAuth.instance.signOut();
        ShowToastDialog.showToast("This user does not exist in the customer app.".tr);
        Get.offAll(() => const LoginScreen());
      }
    } on FirebaseAuthException catch (e) {
      final mfa = ArrowAuthErrors.messageFor(e);
      if (mfa != null) {
        ShowToastDialog.showToast(mfa);
      } else if (e.code == 'user-not-found') {
        ShowToastDialog.showToast("No user found for that email.".tr);
      } else if (e.code == 'wrong-password' || e.code == 'invalid-credential' || e.code == 'INVALID_LOGIN_CREDENTIALS') {
        ShowToastDialog.showToast("Wrong password provided.".tr);
      } else if (e.code == 'invalid-email') {
        ShowToastDialog.showToast("Invalid email.".tr);
      } else {
        ShowToastDialog.showToast(e.message?.tr ?? "Login failed. Please try again.".tr);
      }
    } finally {
      isLoading.value = false;
      ShowToastDialog.closeLoader();
    }
  }

  Future<void> loginWithGoogle() async {
    ShowToastDialog.showLoader("please wait...".tr);
    try {
      final value = await signInWithGoogle();
      ShowToastDialog.closeLoader();
      if (value == null) {
        ShowToastDialog.showToast(ArrowGoogleAuth.developerErrorToast);
        return;
      }
      if (value.additionalUserInfo!.isNewUser) {
        UserModel userModel = UserModel();
        userModel.id = value.user!.uid;
        userModel.email = value.user!.email;
        userModel.firstName = value.user!.displayName?.split(' ').first;
        userModel.lastName = value.user!.displayName?.split(' ').last;
        userModel.provider = 'google';

        Get.off(const SignUpScreen(), arguments: {"userModel": userModel, "type": "google"});
      } else {
        await FireStoreUtils.userExistOrNot(value.user!.uid).then((userExit) async {
          if (userExit == true) {
            UserModel? userModel = await FireStoreUtils.getUserProfile(value.user!.uid);
            if (userModel != null && userModel.role == Constant.userRoleCustomer) {
              if (userModel.active == true) {
                userModel.fcmToken = await NotificationService.getToken();
                await FireStoreUtils.updateUser(userModel);
                await ArrowSecureAuthUi.afterFederatedLogin(
                  Get.context,
                  arrowAuth,
                  email: value.user?.email,
                  method: ArrowLoginMethod.google,
                );

                if (userModel.shippingAddress != null && userModel.shippingAddress!.isNotEmpty) {
                  final defaultAddress = userModel.shippingAddress!.firstWhere((e) => e.isDefault == true, orElse: () => userModel.shippingAddress!.first);

                  Constant.selectedLocation = defaultAddress;

                  Get.offAll(() => const ServiceListScreen());
                } else {
                  Get.offAll(() => const LocationPermissionScreen());
                }
              } else {
                await FirebaseAuth.instance.signOut();
                ShowToastDialog.showToast("This user is disabled. Please contact admin.".tr);
                Get.offAll(() => const LoginScreen());
              }
            } else {
              await FirebaseAuth.instance.signOut();
              ShowToastDialog.showToast("This user does not exist in the customer app.".tr);
              Get.offAll(() => const LoginScreen());
            }
          } else {
            UserModel userModel = UserModel();
            userModel.id = value.user!.uid;
            userModel.email = value.user!.email;
            userModel.firstName = value.user!.displayName?.split(' ').first;
            userModel.lastName = value.user!.displayName?.split(' ').last;
            userModel.provider = 'google';

            Get.off(const SignUpScreen(), arguments: {"userModel": userModel, "type": "google"});
          }
        });
      }
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(ArrowGoogleAuth.userMessage(e));
    }
  }

  Future<void> loginWithApple() async {
    ShowToastDialog.showLoader("please wait...".tr);
    await signInWithApple().then((value) async {
      ShowToastDialog.closeLoader();
      if (value != null) {
        Map<String, dynamic> map = value;
        AuthorizationCredentialAppleID appleCredential = map['appleCredential'];
        UserCredential userCredential = map['userCredential'];

        print("isNewUser: ${userCredential.additionalUserInfo!.isNewUser}");
        print("apple email: ${appleCredential.email}");
        if (userCredential.additionalUserInfo!.isNewUser) {
          // New user → go to sign-up
          UserModel userModel = UserModel();
          userModel.id = userCredential.user!.uid;
          userModel.email = appleCredential.email ?? userCredential.user!.email;
          userModel.firstName = appleCredential.givenName ?? "";
          userModel.lastName = appleCredential.familyName ?? "";
          userModel.provider = 'apple';

          Get.off(const SignUpScreen(), arguments: {"userModel": userModel, "type": "apple"});
        } else {
          // Existing user
          await FireStoreUtils.userExistOrNot(userCredential.user!.uid).then((userExit) async {
            if (userExit == true) {
              UserModel? userModel = await FireStoreUtils.getUserProfile(userCredential.user!.uid);
              if (userModel != null && userModel.role == Constant.userRoleCustomer) {
                if (userModel.active == true) {
                  userModel.fcmToken = await NotificationService.getToken();
                  await FireStoreUtils.updateUser(userModel);
                  await ArrowSecureAuthUi.afterFederatedLogin(
                    Get.context,
                    arrowAuth,
                    email: userCredential.user?.email ?? appleCredential.email,
                    method: ArrowLoginMethod.apple,
                  );

                  if (userModel.shippingAddress != null && userModel.shippingAddress!.isNotEmpty) {
                    final defaultAddress = userModel.shippingAddress!.firstWhere((e) => e.isDefault == true, orElse: () => userModel.shippingAddress!.first);

                    Constant.selectedLocation = defaultAddress;
                    Get.offAll(() => const ServiceListScreen());
                  } else {
                    Get.offAll(() => const LocationPermissionScreen());
                  }
                } else {
                  await FirebaseAuth.instance.signOut();
                  ShowToastDialog.showToast("This user is disabled. Please contact admin.".tr);
                  Get.offAll(() => const LoginScreen());
                }
              } else {
                await FirebaseAuth.instance.signOut();
                ShowToastDialog.showToast("This user does not exist in the customer app.".tr);
                Get.offAll(() => const LoginScreen());
              }
            } else {
              // User not in DB → go to signup
              UserModel userModel = UserModel();
              userModel.id = userCredential.user!.uid;
              userModel.email = appleCredential.email ?? userCredential.user!.email;
              userModel.firstName = appleCredential.givenName ?? "";
              userModel.lastName = appleCredential.familyName ?? "";
              userModel.provider = 'apple';

              Get.off(const SignUpScreen(), arguments: {"userModel": userModel, "type": "apple"});
            }
          });
        }
      }
    });
  }

  Future<UserCredential?> signInWithGoogle() async {
    return ArrowGoogleAuth.signIn();
  }

  String sha256ofString(String input) {
    final bytes = utf8.encode(input);
    final digest = sha256.convert(bytes);
    return digest.toString();
  }

  Future<Map<String, dynamic>?> signInWithApple() async {
    try {
      final rawNonce = generateNonce();
      final nonce = sha256ofString(rawNonce);

      AuthorizationCredentialAppleID appleCredential = await SignInWithApple.getAppleIDCredential(scopes: [AppleIDAuthorizationScopes.email, AppleIDAuthorizationScopes.fullName], nonce: nonce);

      final oauthCredential = OAuthProvider("apple.com").credential(idToken: appleCredential.identityToken, rawNonce: rawNonce, accessToken: appleCredential.authorizationCode);

      UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(oauthCredential);
      return {"appleCredential": appleCredential, "userCredential": userCredential};
    } catch (e) {
      debugPrint(e.toString());
    }
    return null;
  }
}
