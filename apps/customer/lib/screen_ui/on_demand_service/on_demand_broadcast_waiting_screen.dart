import 'package:customer/controllers/on_demand_broadcast_waiting_controller.dart';
import 'package:customer/controllers/theme_controller.dart';
import 'package:customer/themes/app_them_data.dart';
import 'package:customer/themes/round_button_fill.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class OnDemandBroadcastWaitingScreen extends StatelessWidget {
  const OnDemandBroadcastWaitingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Get.find<ThemeController>().isDark.value;
    return GetX(
      init: OnDemandBroadcastWaitingController(),
      builder: (OnDemandBroadcastWaitingController controller) {
        final order = controller.order.value;
        return Scaffold(
          appBar: AppBar(
            backgroundColor: AppThemeData.primary300,
            automaticallyImplyLeading: false,
            title: Text("Procurando prestador próximo…".tr, style: AppThemeData.boldTextStyle(fontSize: 18, color: AppThemeData.grey900)),
          ),
          body: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                const SizedBox(height: 24),
                const CircularProgressIndicator(),
                const SizedBox(height: 24),
                Text(
                  "Estamos avisando prestadores próximos. O primeiro que aceitar fica com o pedido.".tr,
                  textAlign: TextAlign.center,
                  style: AppThemeData.mediumTextStyle(fontSize: 16, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey800),
                ),
                const SizedBox(height: 16),
                Text(
                  order?.provider.title ?? '',
                  textAlign: TextAlign.center,
                  style: AppThemeData.semiBoldTextStyle(fontSize: 18, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900),
                ),
                if ((order?.address?.getFullAddress() ?? '').isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Text(
                    order!.address!.getFullAddress(),
                    textAlign: TextAlign.center,
                    style: AppThemeData.mediumTextStyle(fontSize: 14, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500),
                  ),
                ],
                const Spacer(),
                RoundedButtonFill(
                  title: "Cancelar busca".tr,
                  color: AppThemeData.grey200,
                  textColor: AppThemeData.grey900,
                  onPress: controller.cancelSearch,
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
