import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:vendor/lang/app_ar.dart';
import 'package:vendor/lang/app_de.dart';
import 'package:vendor/lang/app_en.dart';
import 'package:vendor/lang/app_fr.dart';
import 'package:vendor/lang/app_hi.dart';
import 'package:vendor/lang/app_ja.dart';
import 'package:vendor/lang/app_pt.dart';
import 'package:vendor/lang/app_ru.dart';
import 'package:vendor/lang/app_zh.dart';

class LocalizationService extends Translations {
  // Default alinhado ao slug ativo no painel: pt_br
  static const locale = Locale('pt', 'BR');

  static final locales = [
    const Locale('pt', 'BR'),
    const Locale('en'),
    const Locale('fr'),
    const Locale('zh'),
    const Locale('ja'),
    const Locale('hi'),
    const Locale('de'),
    const Locale('ru'),
    const Locale('ar'),
  ];

  @override
  Map<String, Map<String, String>> get keys => {
        'pt_BR': ptPO,
        'pt_br': ptPO,
        'pt': ptPO,
        'en': enUS,
        'fr': trFR,
        'zh': zhCH,
        'ja': jaJP,
        'hi': hiIN,
        'de': deGR,
        'ru': ruRU,
        'ar': lnAr,
      };

  void changeLocale(String lang) {
    final normalized = lang.toLowerCase().replaceAll('-', '_');
    if (normalized == 'pt_br' || normalized == 'pt') {
      Get.updateLocale(const Locale('pt', 'BR'));
      return;
    }
    Get.updateLocale(Locale(lang));
  }
}
