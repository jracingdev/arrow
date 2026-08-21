import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:flutter/material.dart';

class DriverAuthSettingsTiles extends StatelessWidget {
  const DriverAuthSettingsTiles({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = ArrowSecureAuth.forApp(ArrowAndroidPackages.driver);
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        ArrowBiometricSettingsTile(auth: auth),
        ArrowForgetDeviceTile(auth: auth),
      ],
    );
  }
}
