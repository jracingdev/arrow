import 'package:customer/lang/app_ar.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../lang/app_en.dart';
import '../lang/app_pt.dart';

class LocalizationService extends Translations {
  // Default alinhado ao slug ativo no painel: pt_br
  static const locale = Locale('pt', 'BR');

  static final locales = [
    const Locale('pt', 'BR'),
    const Locale('en'),
    const Locale('ar'),
  ];

  // Chaves GetX: slug admin `pt_br` + variantes
  @override
  Map<String, Map<String, String>> get keys => {
        'pt_BR': ptBR,
        'pt_br': ptBR,
        'pt': ptBR,
        'en_US': enUS,
        'en': enUS,
        'ar_AR': arAR,
        'ar': arAR,
      };

  void changeLocale(String lang) {
    final normalized = lang.toLowerCase().replaceAll('-', '_');
    if (normalized == 'pt_br' || normalized == 'pt' || normalized == 'pt_br_br') {
      Get.updateLocale(const Locale('pt', 'BR'));
      return;
    }
    if (normalized.contains('_')) {
      final parts = normalized.split('_');
      Get.updateLocale(Locale(parts[0], parts.length > 1 ? parts[1].toUpperCase() : ''));
      return;
    }
    Get.updateLocale(Locale(lang));
  }
}
