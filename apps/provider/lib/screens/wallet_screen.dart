import 'package:flutter/material.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/themes/app_theme.dart';

class WalletScreen extends StatelessWidget {
  const WalletScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final amount = Constant.userModel?.walletAmount ?? 0;
    return Scaffold(
      appBar: AppBar(title: const Text('Carteira')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Saldo', style: TextStyle(color: AppTheme.grey500)),
            const SizedBox(height: 8),
            Text(
              'R\$ ${amount.toStringAsFixed(2)}',
              style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w700, color: AppTheme.grey900),
            ),
            const SizedBox(height: 16),
            const Text(
              'O saldo vem de users.wallet_amount. Saques e extrato completo entram numa próxima versão.',
              style: TextStyle(color: AppTheme.grey500),
            ),
          ],
        ),
      ),
    );
  }
}
