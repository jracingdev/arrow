import 'package:customer/constant/constant.dart';
import 'package:customer/controllers/theme_controller.dart';
import 'package:customer/models/user_model.dart';
import 'package:customer/screen_ui/location_enable_screens/address_list_screen.dart';
import 'package:customer/screen_ui/service_home_screen/service_list_screen.dart';
import 'package:customer/themes/app_them_data.dart';
import 'package:customer/themes/round_button_fill.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:customer/widget/osm_map/map_picker_page.dart';
import 'package:customer/widget/place_picker/location_picker_screen.dart';
import 'package:customer/widget/place_picker/selected_location_model.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';

import '../../constant/assets.dart';
import '../../utils/utils.dart';

class LocationPermissionScreen extends StatelessWidget {
  const LocationPermissionScreen({super.key});

  Future<void> _openMapPicker(BuildContext context, ShippingAddress addressModel) async {
    final useOsm = (Constant.selectedMapType ?? 'osm').isEmpty || Constant.selectedMapType == 'osm';
    if (useOsm) {
      final result = await Get.to(() => MapPickerPage());
      if (result != null) {
        final firstPlace = result;
        addressModel.addressAs = "Home";
        addressModel.locality = firstPlace.address.toString();
        addressModel.location = UserLocation(
          latitude: firstPlace.coordinates.latitude,
          longitude: firstPlace.coordinates.longitude,
        );
        Constant.selectedLocation = addressModel;
        Get.offAll(const ServiceListScreen());
      }
      return;
    }

    final value = await Get.to(LocationPickerScreen());
    if (value != null) {
      SelectedLocationModel selectedLocationModel = value;
      addressModel.addressAs = "Home";
      addressModel.locality = Utils.formatAddress(selectedLocation: selectedLocationModel);
      addressModel.location = UserLocation(
        latitude: selectedLocationModel.latLng!.latitude,
        longitude: selectedLocationModel.latLng!.longitude,
      );
      Constant.selectedLocation = addressModel;
      Get.offAll(const ServiceListScreen());
    }
  }

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();
    final isDark = themeController.isDark.value;
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                SizedBox(height: 20),
                Image.asset(AppAssets.icLocation),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 25),
                  child: Text(
                    "Enable Location for a Personalized Experience".tr,
                    style: AppThemeData.boldTextStyle(fontSize: 24, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900),
                    textAlign: TextAlign.center,
                  ),
                ),
                const SizedBox(height: 10),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 50),
                  child: Text(
                    "Allow location access to discover beauty stores and services near you.".tr,
                    style: AppThemeData.mediumTextStyle(fontSize: 14, color: isDark ? AppThemeData.greyDark600 : AppThemeData.grey600),
                    textAlign: TextAlign.center,
                  ),
                ),
                const SizedBox(height: 30),
                RoundedButtonFill(
                  title: "Use current location".tr,
                  onPress: () async {
                    Constant.checkPermission(
                      context: context,
                      onTap: () async {
                        ShowToastDialog.showLoader("Please wait...".tr);
                        try {
                          final addressModel = await Utils.buildAddressFromCurrentPosition();
                          ShowToastDialog.closeLoader();
                          if (addressModel == null) {
                            ShowToastDialog.showToast(
                              "Could not get your location. Enable GPS and try again, or set from map.".tr,
                            );
                            return;
                          }
                          Constant.selectedLocation = addressModel;
                          // Reuse GPS from address build — avoid a second hang on getCurrentPosition.
                          if (addressModel.location?.latitude != null && addressModel.location?.longitude != null) {
                            Constant.currentLocation = Position(
                              latitude: addressModel.location!.latitude!,
                              longitude: addressModel.location!.longitude!,
                              timestamp: DateTime.now(),
                              accuracy: 0,
                              altitude: 0,
                              altitudeAccuracy: 0,
                              heading: 0,
                              headingAccuracy: 0,
                              speed: 0,
                              speedAccuracy: 0,
                            );
                          }
                          Get.offAll(const ServiceListScreen());
                        } catch (e) {
                          ShowToastDialog.closeLoader();
                          ShowToastDialog.showToast(
                            "Could not get your location. Enable GPS and try again, or set from map.".tr,
                          );
                        }
                      },
                    );
                  },
                  color: AppThemeData.grey900,
                  textColor: AppThemeData.grey50,
                ),
                const SizedBox(height: 10),
                RoundedButtonFill(
                  title: "Set from map".tr,
                  onPress: () async {
                    Constant.checkPermission(
                      context: context,
                      onTap: () async {
                        ShowToastDialog.showLoader("Please wait...".tr);
                        ShippingAddress addressModel = ShippingAddress();
                        try {
                          // Warm up GPS when possible; still open the map if it fails.
                          await Utils.getCurrentLocation();
                          ShowToastDialog.closeLoader();
                          await _openMapPicker(context, addressModel);
                        } catch (e) {
                          ShowToastDialog.closeLoader();
                          await _openMapPicker(context, addressModel);
                        }
                      },
                    );
                  },
                  color: AppThemeData.grey50,
                  textColor: AppThemeData.grey900,
                ),
                const SizedBox(height: 20),
                Constant.userModel == null
                    ? const SizedBox()
                    : GestureDetector(
                      onTap: () async {
                        Get.to(AddressListScreen())!.then((value) {
                          if (value != null) {
                            ShippingAddress addressModel = value;
                            Constant.selectedLocation = addressModel;
                            Get.offAll(const ServiceListScreen());
                          }
                        });
                      },
                      child: Text("Enter Manually location".tr, style: AppThemeData.semiBoldTextStyle(fontSize: 16, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900)),
                    ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
