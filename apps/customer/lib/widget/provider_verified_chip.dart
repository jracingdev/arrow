import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:customer/themes/app_them_data.dart';

class ProviderVerifiedChip extends StatelessWidget {
  const ProviderVerifiedChip({super.key, required this.verified, this.compact = true});

  final bool verified;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    if (!verified) return const SizedBox.shrink();
    return Tooltip(
      message: 'Documentação verificada pela plataforma',
      child: Container(
        margin: const EdgeInsets.only(left: 6),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: const Color(0xFF16A34A).withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.verified, size: 14, color: Color(0xFF16A34A)),
            const SizedBox(width: 4),
            Text(
              compact ? 'Profissional verificado'.tr : 'Documentação verificada pela plataforma'.tr,
              style: AppThemeData.mediumTextStyle(fontSize: 11, color: const Color(0xFF16A34A)),
            ),
          ],
        ),
      ),
    );
  }
}
