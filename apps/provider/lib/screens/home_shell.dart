import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/screens/agenda_screen.dart';
import 'package:provider/screens/dashboard_screen.dart';
import 'package:provider/screens/documents_screen.dart';
import 'package:provider/screens/home_shell_controller.dart';
import 'package:provider/screens/orders_screen.dart';
import 'package:provider/screens/profile_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  static const _pages = [
    DashboardScreen(),
    OrdersScreen(),
    AgendaScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    final shell = Get.put(HomeShellController());
    final uid = FireStoreUtils.getCurrentUid();
    return StreamBuilder<UserModel?>(
      stream: FireStoreUtils.watchUser(uid),
      builder: (context, snapshot) {
        final user = snapshot.data ?? Constant.userModel;
        if (user != null) Constant.userModel = user;
        final showBanner = user?.needsDocumentVerification == true;
        return Obx(() {
          final index = shell.index.value;
          return Scaffold(
            body: Column(
              children: [
                if (showBanner)
                  Material(
                    color: const Color(0xFFFFF7ED),
                    child: SafeArea(
                      bottom: false,
                      child: InkWell(
                        onTap: () => Get.to(() => const DocumentsScreen()),
                        child: const Padding(
                          padding: EdgeInsets.fromLTRB(16, 10, 16, 10),
                          child: Row(
                            children: [
                              Icon(Icons.badge_outlined, color: AppTheme.warning),
                              SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  'Envie seus documentos para verificação. Você pode usar o app enquanto o administrador analisa.',
                                  style: TextStyle(fontSize: 13),
                                ),
                              ),
                              Icon(Icons.chevron_right, color: AppTheme.grey500),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                Expanded(child: IndexedStack(index: index, children: _pages)),
              ],
            ),
            bottomNavigationBar: BottomNavigationBar(
              currentIndex: index,
              onTap: shell.goTo,
              selectedItemColor: AppTheme.primary,
              items: const [
                BottomNavigationBarItem(icon: Icon(Icons.home_outlined), label: 'Início'),
                BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), label: 'Pedidos'),
                BottomNavigationBarItem(icon: Icon(Icons.calendar_today_outlined), label: 'Agenda'),
                BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Perfil'),
              ],
            ),
          );
        });
      },
    );
  }
}
