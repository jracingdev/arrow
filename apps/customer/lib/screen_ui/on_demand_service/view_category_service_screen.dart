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
        final visible = controller.visibleProviders;
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
                      Row(
                        children: [
                          Expanded(
                            child: _ModeCard(
                              isDark: isDark,
                              icon: Icons.flash_on,
                              title: 'Pedir agora (próximos)'.tr,
                              subtitle: 'Prestadores disponíveis perto de você. O primeiro que aceitar fica com o pedido.'.tr,
                              onTap: () => _openBroadcast(controller),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: _ModeCard(
                              isDark: isDark,
                              icon: Icons.person_search_outlined,
                              title: 'Escolher profissional'.tr,
                              subtitle: 'Lista por distância, nota e documentação verificada.'.tr,
                              onTap: () {},
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              'Escolher profissional'.tr,
                              style: AppThemeData.semiBoldTextStyle(fontSize: 14, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900),
                            ),
                          ),
                          FilterChip(
                            selected: controller.verifiedOnly.value,
                            label: Text('Só verificados'.tr),
                            onSelected: (value) => controller.verifiedOnly.value = value,
                          ),
                        ],
                      ),
                      Align(
                        alignment: Alignment.centerLeft,
                        child: Text(
                          'Ordenado por distância, depois nota. Profissionais verificados primeiro.'.tr,
                          style: AppThemeData.mediumTextStyle(fontSize: 12, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Expanded(
                        child: visible.isEmpty
                            ? Constant.showEmptyView(message: "No Service Found".tr)
                            : ListView.builder(
                                itemCount: visible.length,
                                padding: EdgeInsets.zero,
                                itemBuilder: (context, index) {
                                  ProviderServiceModel providerModel = visible[index];
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

class _ModeCard extends StatelessWidget {
  const _ModeCard({
    required this.isDark,
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final bool isDark;
  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: isDark ? AppThemeData.greyDark50 : AppThemeData.grey50,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(icon, color: AppThemeData.primary300),
              const SizedBox(height: 8),
              Text(title, style: AppThemeData.semiBoldTextStyle(fontSize: 14, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900)),
              const SizedBox(height: 4),
              Text(subtitle, style: AppThemeData.mediumTextStyle(fontSize: 11, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500)),
            ],
          ),
        ),
      ),
    );
  }
}
