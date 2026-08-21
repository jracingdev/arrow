import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/arrow_secure_auth.dart';
import 'package:arrow_shared/arrow_secure_auth_ui.dart';
import 'package:arrow_shared/rating_average.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/provider_document_model.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/screens/documents_screen.dart';
import 'package:provider/screens/home_shell_controller.dart';
import 'package:provider/screens/login_screen.dart';
import 'package:provider/screens/ratings_screen.dart';
import 'package:provider/screens/services_screen.dart';
import 'package:provider/screens/wallet_screen.dart';
import 'package:provider/screens/workers_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Perfil')),
      body: StreamBuilder<UserModel?>(
        stream: FireStoreUtils.watchUser(uid),
        builder: (context, snapshot) {
          final user = snapshot.data ?? Constant.userModel;
          if (user != null) Constant.userModel = user;
          final avg = RatingAverage.formatted(user?.reviewsSum, user?.reviewsCount);
          final count = user?.reviewsCount ?? 0;
          final verified = user?.isDocumentVerify == true;
          return ListView(
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
              const SizedBox(height: 12),
              Align(
                alignment: Alignment.centerLeft,
                child: StreamBuilder<ProviderDocumentModel?>(
                  stream: FireStoreUtils.watchDocumentsVerify(uid),
                  builder: (context, verifySnap) {
                    final rejected = verified != true && (verifySnap.data?.hasRejected == true);
                    final color = verified ? AppTheme.success : (rejected ? const Color(0xFFB91C1C) : AppTheme.warning);
                    return InkWell(
                      onTap: verified ? null : () => Get.to(() => const DocumentsScreen()),
                      child: Chip(
                        avatar: Icon(
                          verified ? Icons.verified : (rejected ? Icons.cancel_outlined : Icons.hourglass_top),
                          size: 16,
                          color: color,
                        ),
                        label: Text(verified ? 'Verificado' : (rejected ? 'Recusado' : 'Verificação pendente')),
                        backgroundColor: color.withValues(alpha: 0.12),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 8),
              Text('Nota $avg · $count avaliações', style: const TextStyle(fontWeight: FontWeight.w600)),
              const SizedBox(height: 16),
              ListTile(
                leading: const Icon(Icons.account_balance_wallet_outlined),
                title: const Text('Carteira'),
                subtitle: Text('R\$ ${(user?.walletAmount ?? 0).toStringAsFixed(2)}'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Get.to(() => const WalletScreen()),
              ),
              ListTile(
                leading: const Icon(Icons.handyman_outlined),
                title: const Text('Serviços'),
                subtitle: const Text('Publicar ou pausar catálogo'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Get.to(() => const ServicesScreen()),
              ),
              ListTile(
                leading: const Icon(Icons.groups_outlined),
                title: const Text('Equipe'),
                subtitle: const Text('Profissionais e disponibilidade'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Get.to(() => const WorkersScreen()),
              ),
              ListTile(
                leading: const Icon(Icons.star_outline),
                title: const Text('Avaliações'),
                subtitle: Text('$count avaliações de clientes'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Get.to(() => const RatingsScreen()),
              ),
              ListTile(
                leading: const Icon(Icons.badge_outlined),
                title: const Text('Documentos'),
                subtitle: Text(verified ? 'Documentos aprovados' : 'Enviar para verificação'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Get.to(() => const DocumentsScreen()),
              ),
              const Divider(),
              ArrowBiometricSettingsTile(auth: ArrowSecureAuth.forApp(ArrowAndroidPackages.provider)),
              ArrowForgetDeviceTile(auth: ArrowSecureAuth.forApp(ArrowAndroidPackages.provider)),
              const Padding(
                padding: EdgeInsets.fromLTRB(16, 8, 16, 8),
                child: Text(
                  'Denúncias verificadas e reincidentes podem levar à suspensão da conta.',
                  style: TextStyle(color: AppTheme.grey500, fontSize: 13),
                ),
              ),
              ListTile(
                leading: const Icon(Icons.logout, color: AppTheme.danger),
                title: const Text('Sair', style: TextStyle(color: AppTheme.danger)),
                onTap: () async {
                  Constant.userModel = null;
                  if (Get.isRegistered<HomeShellController>()) {
                    Get.delete<HomeShellController>();
                  }
                  await FirebaseAuth.instance.signOut();
                  Get.offAll(() => const LoginScreen());
                },
              ),
            ],
          );
        },
      ),
    );
  }
}
