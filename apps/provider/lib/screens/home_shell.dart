import 'package:flutter/material.dart';
import 'package:provider/screens/orders_screen.dart';
import 'package:provider/screens/profile_screen.dart';
import 'package:provider/screens/services_screen.dart';
import 'package:provider/screens/workers_screen.dart';
import 'package:provider/themes/app_theme.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int index = 0;

  static const _pages = [
    OrdersScreen(),
    ServicesScreen(),
    WorkersScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: index, children: _pages),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: index,
        onTap: (value) => setState(() => index = value),
        selectedItemColor: AppTheme.primary,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), label: 'Pedidos'),
          BottomNavigationBarItem(icon: Icon(Icons.handyman_outlined), label: 'Serviços'),
          BottomNavigationBarItem(icon: Icon(Icons.groups_outlined), label: 'Equipe'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Perfil'),
        ],
      ),
    );
  }
}
