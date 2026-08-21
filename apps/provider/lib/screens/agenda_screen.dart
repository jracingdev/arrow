import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/screens/order_detail_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class AgendaScreen extends StatelessWidget {
  const AgendaScreen({super.key});

  static const _upcoming = [
    Constant.orderAccepted,
    Constant.orderAssigned,
    Constant.orderOngoing,
    Constant.inTransit,
  ];

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Agenda')),
      body: StreamBuilder<List<ProviderOrderModel>>(
        stream: FireStoreUtils.watchMyOrders(uid),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }
          final orders = (snapshot.data ?? []).where((o) => _upcoming.contains(o.status)).toList()
            ..sort((a, b) {
              final da = (a.newScheduleDateTime ?? a.scheduleDateTime ?? a.createdAt)?.millisecondsSinceEpoch ?? 0;
              final db = (b.newScheduleDateTime ?? b.scheduleDateTime ?? b.createdAt)?.millisecondsSinceEpoch ?? 0;
              return da.compareTo(db);
            });
          if (orders.isEmpty) {
            return const Center(child: Text('Nenhum atendimento agendado.'));
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: orders.length,
            separatorBuilder: (_, _) => const SizedBox(height: 10),
            itemBuilder: (context, i) {
              final order = orders[i];
              final when = order.newScheduleDateTime ?? order.scheduleDateTime;
              final date = when == null ? 'Horário a confirmar' : DateFormat('dd/MM/yyyy HH:mm').format(when.toDate());
              final hourly = order.provider.priceUnit.toLowerCase() == 'hourly';
              return ListTile(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: AppTheme.grey200),
                ),
                leading: CircleAvatar(
                  backgroundColor: AppTheme.primary.withValues(alpha: 0.12),
                  child: Icon(hourly ? Icons.timer_outlined : Icons.event_outlined, color: AppTheme.primary),
                ),
                title: Text(order.provider.title.isEmpty ? 'Serviço' : order.provider.title),
                subtitle: Text('$date\n${order.author.fullName()}'),
                isThreeLine: true,
                trailing: Text(
                  hourly ? 'Por hora' : 'Preço fixo',
                  style: const TextStyle(fontSize: 12, color: AppTheme.grey500),
                ),
                onTap: () => Get.to(() => OrderDetailScreen(orderId: order.id)),
              );
            },
          );
        },
      ),
    );
  }
}
