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
        final expired = controller.noProvider.value;
        return Scaffold(
          appBar: AppBar(
            backgroundColor: AppThemeData.primary300,
            automaticallyImplyLeading: false,
            title: Text(
              expired ? "Nenhum prestador disponível".tr : "Procurando prestador próximo…".tr,
              style: AppThemeData.boldTextStyle(fontSize: 18, color: AppThemeData.grey900),
            ),
          ),
          body: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                const SizedBox(height: 24),
                if (expired)
                  const Icon(Icons.person_off_outlined, size: 64, color: AppThemeData.grey500)
                else
                  const CircularProgressIndicator(),
                const SizedBox(height: 24),
                Text(
                  expired
                      ? "Ninguém aceitou seu pedido em 10 minutos.".tr
                      : "Avisamos o prestador mais próximo. Se ele não responder, chamamos o seguinte.".tr,
                  textAlign: TextAlign.center,
                  style: AppThemeData.mediumTextStyle(fontSize: 16, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey800),
                ),
                if (!expired) ...[
                  const SizedBox(height: 20),
                  Text(
                    controller.countdownLabel(),
                    style: AppThemeData.boldTextStyle(fontSize: 36, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    "Tempo restante".tr,
                    style: AppThemeData.mediumTextStyle(fontSize: 13, color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500),
                  ),
                ],
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
                  title: expired ? "Voltar".tr : "Cancelar busca".tr,
                  color: AppThemeData.grey200,
                  textColor: AppThemeData.grey900,
                  onPress: expired ? controller.leave : controller.cancelSearch,
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
