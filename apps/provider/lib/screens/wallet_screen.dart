import 'package:flutter/material.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/models/wallet_transaction_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:intl/intl.dart';

class WalletScreen extends StatelessWidget {
  const WalletScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Carteira')),
      body: Column(
        children: [
          StreamBuilder<UserModel?>(
            stream: FireStoreUtils.watchUser(uid),
            builder: (context, snapshot) {
              final user = snapshot.data ?? Constant.userModel;
              final amount = user?.walletAmount ?? 0;
              return Container(
                width: double.infinity,
                margin: const EdgeInsets.all(16),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: const Color(0xFF111827),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Saldo', style: TextStyle(color: Colors.white70)),
                    const SizedBox(height: 8),
                    Text(
                      'R\$ ${amount.toStringAsFixed(2).replaceAll('.', ',')}',
                      style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w700, color: Colors.white),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Créditos de pedidos on-demand na mesma carteira da plataforma.',
                      style: TextStyle(color: Colors.white54, fontSize: 12),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton(
                        onPressed: () => _requestPayout(context, amount.toDouble()),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: Colors.white,
                          side: const BorderSide(color: Color(0xFF00A1F1)),
                        ),
                        child: const Text('Solicitar saque'),
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 16),
            child: Align(
              alignment: Alignment.centerLeft,
              child: Text('Extrato', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: StreamBuilder<List<WalletTransactionModel>>(
              stream: FireStoreUtils.watchWalletTransactions(uid),
              builder: (context, snapshot) {
                final txs = snapshot.data ?? [];
                if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (txs.isEmpty) {
                  return const Center(child: Text('Nenhuma transação ainda.'));
                }
                return ListView.separated(
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                  itemCount: txs.length,
                  separatorBuilder: (_, _) => const SizedBox(height: 8),
                  itemBuilder: (context, i) {
                    final tx = txs[i];
                    final credit = tx.isTopup == true;
                    final when = tx.date == null ? '' : DateFormat('dd/MM/yyyy HH:mm').format(tx.date!.toDate());
                    final amount = tx.amount ?? 0;
                    return ListTile(
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                        side: const BorderSide(color: AppTheme.grey200),
                      ),
                      leading: CircleAvatar(
                        backgroundColor: (credit ? AppTheme.success : AppTheme.danger).withValues(alpha: 0.12),
                        child: Icon(
                          credit ? Icons.arrow_downward : Icons.arrow_upward,
                          color: credit ? AppTheme.success : AppTheme.danger,
                        ),
                      ),
                      title: Text(tx.note?.isNotEmpty == true ? tx.note! : (credit ? 'Crédito' : 'Débito')),
                      subtitle: Text([when, if ((tx.orderId ?? '').isNotEmpty) 'Pedido ${tx.orderId}'].join(' · ')),
                      trailing: Text(
                        '${credit ? '+' : '-'} R\$ ${amount.toStringAsFixed(2).replaceAll('.', ',')}',
                        style: TextStyle(
                          fontWeight: FontWeight.w700,
                          color: credit ? AppTheme.success : AppTheme.danger,
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

Future<void> _requestPayout(BuildContext context, double wallet) async {
  final amountController = TextEditingController(text: wallet > 0 ? wallet.toStringAsFixed(2) : '');
  final noteController = TextEditingController();
  final confirmed = await showDialog<bool>(
    context: context,
    builder: (context) {
      return AlertDialog(
        title: const Text('Solicitar saque'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text(
              'O administrador analisa o pedido na mesma fila de repasses dos outros apps.',
              style: TextStyle(color: AppTheme.grey500, fontSize: 13),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: amountController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              decoration: const InputDecoration(labelText: 'Valor', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: noteController,
              decoration: const InputDecoration(labelText: 'Observação (opcional)', border: OutlineInputBorder()),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancelar')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(minimumSize: const Size(88, 40)),
            child: const Text('Enviar'),
          ),
        ],
      );
    },
  );
  if (confirmed != true) return;
  final amount = double.tryParse(amountController.text.replaceAll(',', '.')) ?? 0;
  ShowToastDialog.showLoader('Enviando solicitação...');
  try {
    await FireStoreUtils.requestPayout(amount: amount, note: noteController.text.trim());
    ShowToastDialog.closeLoader();
    ShowToastDialog.showToast('Solicitação de saque enviada ao administrador.');
  } catch (e) {
    ShowToastDialog.closeLoader();
    ShowToastDialog.showToast(e.toString());
  }
}

