import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../themes/app_them_data.dart';

class ArrowProtectionNote extends StatelessWidget {
  const ArrowProtectionNote({super.key, this.isDark = false});

  final bool isDark;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFF16A34A).withValues(alpha: isDark ? 0.16 : 0.08),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFF16A34A).withValues(alpha: 0.28)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.verified_user_outlined, color: Color(0xFF16A34A), size: 22),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Proteção Arrow'.tr,
                  style: AppThemeData.semiBoldTextStyle(
                    fontSize: 14,
                    color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey900,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  'Documentação verificada pela plataforma. Em caso de problema, use denúncia no pedido.'.tr,
                  style: AppThemeData.mediumTextStyle(
                    fontSize: 12,
                    color: isDark ? AppThemeData.greyDark900 : AppThemeData.grey500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
