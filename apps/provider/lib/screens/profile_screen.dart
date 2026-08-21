import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/screens/login_screen.dart';
import 'package:provider/screens/wallet_screen.dart';
import 'package:provider/themes/app_theme.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = Constant.userModel;
    return Scaffold(
      appBar: AppBar(title: const Text('Perfil')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          CircleAvatar(
            radius: 36,
            backgroundColor: AppTheme.primary.withValues(alpha: 0.15),
            child: Text(
              (user?.firstName?.isNotEmpty == true ? user!.firstName![0] : 'P').toUpperCase(),
              style: const TextStyle(fontSize: 28, color: AppTheme.primary, fontWeight: FontWeight.w700),
            ),
          ),
          const SizedBox(height: 16),
          Text(user?.fullName().isNotEmpty == true ? user!.fullName() : 'Prestador', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
          const SizedBox(height: 4),
          Text(user?.email ?? '', style: const TextStyle(color: AppTheme.grey500)),
          Text(user?.phoneNumber ?? '', style: const TextStyle(color: AppTheme.grey500)),
          const SizedBox(height: 24),
          ListTile(
            leading: const Icon(Icons.account_balance_wallet_outlined),
            title: const Text('Carteira'),
            subtitle: Text('R\$ ${(user?.walletAmount ?? 0).toStringAsFixed(2)}'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => Get.to(() => const WalletScreen()),
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.logout, color: AppTheme.danger),
            title: const Text('Sair', style: TextStyle(color: AppTheme.danger)),
            onTap: () async {
              Constant.userModel = null;
              await FirebaseAuth.instance.signOut();
              Get.offAll(() => const LoginScreen());
            },
          ),
        ],
      ),
    );
  }
}
