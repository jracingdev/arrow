import 'package:customer/controllers/on_demand_booking_controller.dart';
import 'package:customer/screen_ui/auth_screens/login_screen.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_booking_screen.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../constant/constant.dart';
import '../../controllers/theme_controller.dart';
import '../../controllers/view_category_service_controller.dart';
import '../../models/provider_serivce_model.dart';
import '../../screen_ui/on_demand_service/on_demand_home_screen.dart';
import '../../themes/app_them_data.dart';

class ViewCategoryServiceListScreen extends StatelessWidget {
  const ViewCategoryServiceListScreen({super.key});

  void _openBroadcast(ViewCategoryServiceController controller) {
    if (Constant.userModel == null) {
      Get.offAll(const LoginScreen());
      return;
    }
    if (Get.isRegistered<OnDemandBookingController>()) {
      Get.delete<OnDemandBookingController>(force: true);
    }
    Get.to(
      () => const OnDemandBookingScreen(),
      arguments: {
        'broadcast': true,
        'categoryId': controller.categoryId.value,
        'categoryTitle': controller.categoryTitle.value,
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();
    final isDark = themeController.isDark.value;

    return GetX<ViewCategoryServiceController>(
      init: ViewCategoryServiceController(),
      builder: (controller) {
        return Scaffold(
          appBar: AppBar(
            automaticallyImplyLeading: false,
            backgroundColor: AppThemeData.primary300,
            title: Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Row(
                children: [
                  GestureDetector(
                    onTap: () => Get.back(),
                    child: Container(
                      height: 42,
                      width: 42,
                      decoration: BoxDecoration(shape: BoxShape.circle, color: AppThemeData.grey50),
                      child: Center(child: Padding(padding: const EdgeInsets.only(left: 5), child: Icon(Icons.arrow_back_ios, color: AppThemeData.grey900, size: 20))),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(child: Text(controller.categoryTitle.value, style: AppThemeData.boldTextStyle(fontSize: 18, color: AppThemeData.grey900))),
                ],
              ),
            ),
          ),
          body: controller.isLoading.value
              ? Constant.loader()
              : Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  child: Column(
                    children: [
                      Material(
                        color: isDark ? AppThemeData.greyDark50 : AppThemeData.grey50,
                        borderRadius: BorderRadius.circular(12),
                        child: InkWell(
                          borderRadius: BorderRadius.circular(12),
                          onTap: () => _openBroadcast(controller),
                          child: Padding(
                            padding: const EdgeInsets.all(14),
                            child: Row(
                              children: [
                                Icon(Icons.near_me, color: AppThemeData.primary300),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text("Pedir prestador próximo".tr, style: AppThemeData.semiBoldTextStyle(fontSize: 16, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900)),
                                      Text(
                                        "Como um chamado: prestadores perto de você veem o pedido e o primeiro que aceitar fica com ele.".tr,
                                        style: AppThemeData.mediumTextStyle(fontSize: 12, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.chevron_right),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Align(
                        alignment: Alignment.centerLeft,
                        child: Text("Mais próximos primeiro".tr, style: AppThemeData.semiBoldTextStyle(fontSize: 14, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900)),
                      ),
                      const SizedBox(height: 8),
                      Expanded(
                        child: controller.providerList.isEmpty
                            ? Constant.showEmptyView(message: "No Service Found".tr)
                            : ListView.builder(
                                itemCount: controller.providerList.length,
                                padding: EdgeInsets.zero,
                                itemBuilder: (context, index) {
                                  ProviderServiceModel providerModel = controller.providerList[index];
                                  return ServiceView(isDark: isDark, provider: providerModel, controller: controller.onDemandHomeController.value);
                                },
                              ),
                      ),
                    ],
                  ),
                ),
        );
      },
    );
  }
}
