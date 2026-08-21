import 'dart:convert';
import 'package:arrow_shared/arrow_auth_errors.dart';
import 'package:arrow_shared/arrow_google_auth.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:crypto/crypto.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';
import 'package:vendor/app/auth_screen/signup_screen.dart';
import 'package:vendor/app/dash_board_screens/app_not_access_screen.dart';
import 'package:vendor/app/dash_board_screens/dash_board_screen.dart';
import 'package:vendor/app/subscription_plan_screen/subscription_plan_screen.dart';
import 'package:vendor/constant/constant.dart';
import 'package:vendor/constant/show_toast_dialog.dart';
import 'package:vendor/models/user_model.dart';
import 'package:vendor/models/vendor_model.dart';
import 'package:vendor/utils/fire_store_utils.dart';
import 'package:vendor/utils/notification_service.dart';
import 'package:flutter/material.dart';

class LoginController extends GetxController {
  Rx<TextEditingController> emailEditingControllerOwner = TextEditingController().obs;
  Rx<TextEditingController> passwordEditingControllerOwner = TextEditingController().obs;
  RxBool passwordVisibleOwner = true.obs;

  Rx<TextEditingController> emailEditingControllerEmployee = TextEditingController().obs;
  Rx<TextEditingController> passwordEditingControllerEmployee = TextEditingController().obs;
  RxBool passwordVisible = true.obs;

  RxInt selectedTabbar = 0.obs;
  RxBool rememberMe = true.obs;
  RxBool showBiometricLogin = false.obs;
  final ArrowSecureAuth arrowAuth = ArrowSecureAuth.forApp(ArrowAndroidPackages.store);

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
      if (email != null && email.isNotEmpty) {
        emailEditingControllerOwner.value.text = email;
        emailEditingControllerEmployee.value.text = email;
      }
      if (password != null && password.isNotEmpty) {
        passwordEditingControllerOwner.value.text = password;
        passwordEditingControllerEmployee.value.text = password;
      }
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
    if (!value) {
      passwordEditingControllerOwner.value.clear();
      passwordEditingControllerEmployee.value.clear();
    }
  }

  Future<void> forgetDevice() async {
    await arrowAuth.forgetDevice();
    rememberMe.value = false;
    showBiometricLogin.value = false;
    emailEditingControllerOwner.value.clear();
    passwordEditingControllerOwner.value.clear();
    emailEditingControllerEmployee.value.clear();
    passwordEditingControllerEmployee.value.clear();
  }

  Future<void> loginWithBiometrics() async {
    final ok = await arrowAuth.authenticate();
    if (!ok) return;
    final creds = await arrowAuth.passwordCredentials();
    if (creds == null) return;
    if (selectedTabbar.value == 1) {
      emailEditingControllerEmployee.value.text = creds.email;
      passwordEditingControllerEmployee.value.text = creds.password;
      await employeeloginWithEmailAndPassword();
    } else {
      emailEditingControllerOwner.value.text = creds.email;
      passwordEditingControllerOwner.value.text = creds.password;
      await onwerloginWithEmailAndPassword();
    }
  }

  Future<void> _persistPassword(String email, String password) {
    return ArrowSecureAuthUi.afterPasswordLogin(
      Get.context,
      arrowAuth,
      email: email,
      password: password,
      rememberMe: rememberMe.value,
    );
  }

  Future<void> _persistFederated({String? email, required ArrowLoginMethod method}) {
    return ArrowSecureAuthUi.afterFederatedLogin(
      Get.context,
      arrowAuth,
      email: email,
      method: method,
    );
  }

  Future<void> onwerloginWithEmailAndPassword() async {
    ShowToastDialog.showLoader("Please wait.".tr);
    try {
      final credential = await FirebaseAuth.instance.signInWithEmailAndPassword(
        email: emailEditingControllerOwner.value.text.toLowerCase().trim(),
        password: passwordEditingControllerOwner.value.text.trim(),
      );
      UserModel? userModel = await FireStoreUtils.getUserProfile(credential.user!.uid);
      if (userModel != null) {
        if (userModel.role == Constant.userRoleVendor) {
          if (userModel.active == true) {
            try {
              userModel.fcmToken = await NotificationService.getToken();
            } catch (_) {}
            await FireStoreUtils.updateUser(userModel);
            await _persistPassword(
              emailEditingControllerOwner.value.text.toLowerCase().trim(),
              passwordEditingControllerOwner.value.text.trim(),
            );
            bool isPlanExpire = false;
            if (userModel.subscriptionPlan?.id != null) {
              if (userModel.subscriptionExpiryDate == null) {
                isPlanExpire = userModel.subscriptionPlan?.expiryDay != '-1';
              } else {
                isPlanExpire = userModel.subscriptionExpiryDate!.toDate().isBefore(DateTime.now());
              }
            } else {
              isPlanExpire = true;
            }

            if (userModel.sectionId != null && userModel.sectionId!.isNotEmpty) {
              final section = await FireStoreUtils.getSectionById(userModel.sectionId.toString());
              if (section != null) {
                Constant.selectedSection = section;
              }
            }

            if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
              if ((userModel.sectionId ?? '').isEmpty && Constant.isSubscriptionModelApplied == false) {
                Get.offAll(const DashBoardScreen());
              } else {
                Get.offAll(const SubscriptionPlanScreen());
              }
            } else if (userModel.subscriptionPlan?.features?.ownerMobileApp == true) {
              Get.offAll(const DashBoardScreen());
            } else {
              Get.offAll(const AppNotAccessScreen());
            }
          } else {
            await FirebaseAuth.instance.signOut();
            ShowToastDialog.showToast("This user is disable please contact to administrator".tr);
          }
        } else {
          await FirebaseAuth.instance.signOut();
          ShowToastDialog.showToast("This user is not created in Store application.".tr);
        }
      } else {
        await FirebaseAuth.instance.signOut();
        ShowToastDialog.showToast("This user is not created in Store application.".tr);
      }
    } on FirebaseAuthException catch (e) {
      print(e.code);
      final mfa = ArrowAuthErrors.messageFor(e);
      if (mfa != null) {
        ShowToastDialog.showToast(mfa);
      } else if (e.code == 'user-not-found') {
        ShowToastDialog.showToast("No user found for that email.".tr);
      } else if (e.code == 'wrong-password' || e.code == 'invalid-credential' || e.code == 'INVALID_LOGIN_CREDENTIALS') {
        ShowToastDialog.showToast("Wrong password provided for that user.".tr);
      } else if (e.code == 'invalid-email') {
        ShowToastDialog.showToast("Invalid Email.".tr);
      } else {
        ShowToastDialog.showToast(e.message ?? e.code);
      }
    } catch (e) {
      debugPrint('owner email login error: $e');
      ShowToastDialog.showToast("Something went wrong, please contact admin.".tr);
    } finally {
      ShowToastDialog.closeLoader();
    }
  }

  Future<void> employeeloginWithEmailAndPassword() async {
    ShowToastDialog.showLoader("Please wait.".tr);
    try {
      final credential = await FirebaseAuth.instance.signInWithEmailAndPassword(
        email: emailEditingControllerEmployee.value.text.toLowerCase().trim(),
        password: passwordEditingControllerEmployee.value.text.trim(),
      );
      UserModel? userModel = await FireStoreUtils.getUserProfile(credential.user!.uid);
      if (userModel != null) {
        if (userModel.role == Constant.userRoleEmployee) {
          if (userModel.active == true) {
            try {
              userModel.fcmToken = await NotificationService.getToken();
            } catch (_) {}
            await FireStoreUtils.updateUser(userModel);
            await _persistPassword(
              emailEditingControllerEmployee.value.text.toLowerCase().trim(),
              passwordEditingControllerEmployee.value.text.trim(),
            );
            VendorModel? vendor = await FireStoreUtils.getVendorById(userModel.vendorID!);
            bool isPlanExpire = false;
            if (vendor?.subscriptionPlan?.id != null) {
              if (vendor?.subscriptionExpiryDate == null) {
                isPlanExpire = vendor?.subscriptionPlan?.expiryDay != '-1';
              } else {
                isPlanExpire = vendor!.subscriptionExpiryDate!.toDate().isBefore(DateTime.now());
              }
            } else {
              isPlanExpire = true;
            }
            if (vendor?.sectionId != null && vendor!.sectionId!.isNotEmpty) {
              final section = await FireStoreUtils.getSectionById(vendor.sectionId.toString());
              if (section != null) {
                Constant.selectedSection = section;
              }
            }

            if (vendor?.subscriptionPlanId == null || isPlanExpire == true) {
              if ((vendor?.sectionId ?? userModel.sectionId ?? '').isEmpty && Constant.isSubscriptionModelApplied == false) {
                Get.offAll(const DashBoardScreen());
              } else {
                Get.offAll(const SubscriptionPlanScreen());
              }
            } else if (vendor?.subscriptionPlan?.features?.ownerMobileApp == true) {
              Get.offAll(const DashBoardScreen());
            } else {
              Get.offAll(const AppNotAccessScreen());
            }
          } else {
            await FirebaseAuth.instance.signOut();
            ShowToastDialog.showToast("This user is disable please contact to administrator".tr);
          }
        } else {
          await FirebaseAuth.instance.signOut();
          ShowToastDialog.showToast("This user is not created in restaurant application.".tr);
        }
      } else {
        await FirebaseAuth.instance.signOut();
        ShowToastDialog.showToast("This user is not created in restaurant application.".tr);
      }
    } on FirebaseAuthException catch (e) {
      print(e.code);
      final mfa = ArrowAuthErrors.messageFor(e);
      if (mfa != null) {
        ShowToastDialog.showToast(mfa);
      } else if (e.code == 'user-not-found') {
        ShowToastDialog.showToast("No user found for that email.".tr);
      } else if (e.code == 'wrong-password' || e.code == 'invalid-credential' || e.code == 'INVALID_LOGIN_CREDENTIALS') {
        ShowToastDialog.showToast("Wrong password provided for that user.".tr);
      } else if (e.code == 'invalid-email') {
        ShowToastDialog.showToast("Invalid Email.".tr);
      } else {
        ShowToastDialog.showToast(e.message ?? e.code);
      }
    } catch (e) {
      debugPrint('employee email login error: $e');
      ShowToastDialog.showToast("Something went wrong, please contact admin.".tr);
    } finally {
      ShowToastDialog.closeLoader();
    }
  }

  Future<void> loginWithGoogle() async {
    ShowToastDialog.showLoader("please wait...".tr);
    try {
      final value = await signInWithGoogle();
      if (value == null) {
        ShowToastDialog.showToast(ArrowGoogleAuth.developerErrorToast);
        return;
      }
      if (value.additionalUserInfo?.isNewUser == true) {
        UserModel userModel = UserModel();
        userModel.id = value.user!.uid;
        userModel.email = value.user!.email;
        userModel.firstName = value.user!.displayName?.split(' ').first;
        userModel.lastName = value.user!.displayName?.split(' ').last;
        userModel.provider = 'google';

        Get.off(const SignupScreen(), arguments: {"userModel": userModel, "type": "google"});
        return;
      }

      final userExit = await FireStoreUtils.userExistOrNot(value.user!.uid);
      if (userExit != true) {
        UserModel userModel = UserModel();
        userModel.id = value.user!.uid;
        userModel.email = value.user!.email;
        userModel.firstName = value.user!.displayName?.split(' ').first;
        userModel.lastName = value.user!.displayName?.split(' ').last;
        userModel.provider = 'google';

        Get.off(const SignupScreen(), arguments: {"userModel": userModel, "type": "google"});
        return;
      }

      UserModel? userModel = await FireStoreUtils.getUserProfile(value.user!.uid);
      if (userModel == null || userModel.role != Constant.userRoleVendor) {
        await FirebaseAuth.instance.signOut();
        ShowToastDialog.showToast("This user is not created in Store application.".tr);
        return;
      }
      if (userModel.active != true) {
        await FirebaseAuth.instance.signOut();
        ShowToastDialog.showToast("This user is disable please contact to administrator".tr);
        return;
      }

      try {
        userModel.fcmToken = await NotificationService.getToken();
      } catch (_) {}
      await FireStoreUtils.updateUser(userModel);
      Constant.userModel = userModel;
      await _persistFederated(email: userModel.email, method: ArrowLoginMethod.google);

      bool isPlanExpire = false;
      if (userModel.subscriptionPlan?.id != null) {
        if (userModel.subscriptionExpiryDate == null) {
          isPlanExpire = userModel.subscriptionPlan?.expiryDay != '-1';
        } else {
          isPlanExpire = userModel.subscriptionExpiryDate!.toDate().isBefore(DateTime.now());
        }
      } else {
        isPlanExpire = true;
      }

      if (userModel.sectionId != null && userModel.sectionId!.isNotEmpty) {
        final section = await FireStoreUtils.getSectionById(userModel.sectionId.toString());
        if (section != null) {
          Constant.selectedSection = section;
        }
      }

      if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
        if ((userModel.sectionId ?? '').isEmpty && Constant.isSubscriptionModelApplied == false) {
          Get.offAll(const DashBoardScreen());
        } else {
          Get.offAll(const SubscriptionPlanScreen());
        }
      } else if (userModel.subscriptionPlan?.features?.ownerMobileApp == true) {
        Get.offAll(const DashBoardScreen());
      } else {
        Get.offAll(const AppNotAccessScreen());
      }
    } catch (e) {
      ShowToastDialog.showToast(ArrowGoogleAuth.userMessage(e));
      try {
        await FirebaseAuth.instance.signOut();
      } catch (_) {}
    } finally {
      ShowToastDialog.closeLoader();
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
        if (userCredential.additionalUserInfo!.isNewUser) {
          UserModel userModel = UserModel();
          userModel.id = userCredential.user!.uid;
          userModel.email = appleCredential.email;
          userModel.firstName = appleCredential.givenName;
          userModel.lastName = appleCredential.familyName;
          userModel.provider = 'apple';

          ShowToastDialog.closeLoader();
          Get.off(const SignupScreen(), arguments: {"userModel": userModel, "type": "apple"});
        } else {
          await FireStoreUtils.userExistOrNot(userCredential.user!.uid).then((userExit) async {
            ShowToastDialog.closeLoader();
            if (userExit == true) {
              UserModel? userModel = await FireStoreUtils.getUserProfile(userCredential.user!.uid);
              if (userModel!.role == Constant.userRoleVendor) {
                if (userModel.active == true) {
                  userModel.fcmToken = await NotificationService.getToken();
                  await FireStoreUtils.updateUser(userModel);
                  await _persistFederated(email: userModel.email, method: ArrowLoginMethod.apple);
                  bool isPlanExpire = false;
                  if (userModel.subscriptionPlan?.id != null) {
                    if (userModel.subscriptionExpiryDate == null) {
                      if (userModel.subscriptionPlan?.expiryDay == '-1') {
                        isPlanExpire = false;
                      } else {
                        isPlanExpire = true;
                      }
                    } else {
                      DateTime expiryDate = userModel.subscriptionExpiryDate!.toDate();
                      isPlanExpire = expiryDate.isBefore(DateTime.now());
                    }
                  } else {
                    isPlanExpire = true;
                  }
                  if (userModel.sectionId != null) {
                    await FireStoreUtils.getSectionById(userModel.sectionId.toString()).then((value) {
                      if (value != null) {
                        Constant.selectedSection = value;
                      }
                    });
                  }

                  if (userModel.subscriptionPlanId == null || isPlanExpire == true) {
                    if ((userModel.sectionId ?? '').isEmpty && Constant.isSubscriptionModelApplied == false) {
                      Get.offAll(const DashBoardScreen());
                    } else {
                      Get.offAll(const SubscriptionPlanScreen());
                    }
                  } else if (userModel.subscriptionPlan?.features?.ownerMobileApp == true) {
                    Get.offAll(const DashBoardScreen());
                  } else {
                    Get.offAll(const AppNotAccessScreen());
                  }
                } else {
                  await FirebaseAuth.instance.signOut();
                  ShowToastDialog.showToast("This user is disable please contact to administrator".tr);
                }
              } else {
                await FirebaseAuth.instance.signOut();
                // ShowToastDialog.showToast("This user is disable please contact to administrator".tr);
              }
            } else {
              UserModel userModel = UserModel();
              userModel.id = userCredential.user!.uid;
              userModel.email = appleCredential.email;
              userModel.firstName = appleCredential.givenName;
              userModel.lastName = appleCredential.familyName;
              userModel.provider = 'apple';

              Get.off(const SignupScreen(), arguments: {"userModel": userModel, "type": "apple"});
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

      // Request credential for the currently signed in Apple account.
      AuthorizationCredentialAppleID appleCredential = await SignInWithApple.getAppleIDCredential(
        scopes: [AppleIDAuthorizationScopes.email, AppleIDAuthorizationScopes.fullName],
        nonce: nonce,
        // webAuthenticationOptions: WebAuthenticationOptions(clientId: clientID, redirectUri: Uri.parse(redirectURL)),
      );

      // Create an `OAuthCredential` from the credential returned by Apple.
      final oauthCredential = OAuthProvider("apple.com").credential(idToken: appleCredential.identityToken, rawNonce: rawNonce, accessToken: appleCredential.authorizationCode);

      // Sign in the user with Firebase. If the nonce we generated earlier does
      // not match the nonce in `appleCredential.identityToken`, sign in will fail.
      UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(oauthCredential);
      return {"appleCredential": appleCredential, "userCredential": userCredential};
    } catch (e) {
      debugPrint(e.toString());
    }
    return null;
  }
}
