import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/screens/home_shell_controller.dart';
import 'package:provider/screens/order_detail_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/widgets/elapsed_clock.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  static const _tabs = [
    ('Todos', <String>[]),
    ('Novos', Constant.tabPlaced),
    ('Aceitos', Constant.tabAccepted),
    ('Em andamento', Constant.tabOngoing),
    ('Concluídos', Constant.tabCompleted),
    ('Cancelados', Constant.tabCancelled),
  ];

  Color _color(String status) {
    switch (status) {
      case Constant.orderPlaced:
        return AppTheme.warning;
      case Constant.orderAccepted:
      case Constant.orderAssigned:
        return AppTheme.primary;
      case Constant.orderOngoing:
      case Constant.inTransit:
        return const Color(0xFF7C3AED);
      case Constant.orderCompleted:
        return AppTheme.success;
      default:
        return AppTheme.danger;
    }
  }

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    final shell = Get.find<HomeShellController>();
    return Scaffold(
      appBar: AppBar(title: const Text('Pedidos')),
      body: Obx(() {
        final tab = shell.ordersTab.value;
        return Column(
          children: [
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              child: Row(
                children: [
                  for (var i = 0; i < _tabs.length; i++)
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(_tabs[i].$1),
                        selected: tab == i,
                        onSelected: (_) => shell.ordersTab.value = i,
                      ),
                    ),
                ],
              ),
            ),
            Expanded(
              child: StreamBuilder<List<ProviderOrderModel>>(
                stream: FireStoreUtils.watchMyOrders(uid),
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
                    return const Center(child: CircularProgressIndicator());
                  }
                  final allowed = _tabs[tab].$2;
                  final orders = (snapshot.data ?? []).where((o) => allowed.isEmpty || allowed.contains(o.status)).toList();
                  if (orders.isEmpty) {
                    return const Center(child: Text('Nenhum pedido nesta aba.'));
                  }
                  return ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: orders.length,
                    separatorBuilder: (_, _) => const SizedBox(height: 10),
                    itemBuilder: (context, i) {
                      final order = orders[i];
                      final when = order.newScheduleDateTime ?? order.scheduleDateTime ?? order.createdAt;
                      final date = when == null ? '' : DateFormat('dd/MM/yyyy HH:mm').format(when.toDate());
                      return ListTile(
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                          side: const BorderSide(color: AppTheme.grey200),
                        ),
                        title: Text(order.provider.title.isEmpty ? 'Serviço' : order.provider.title),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(order.author.fullName()),
                            if (date.isNotEmpty) Text(date),
                            if (HourlyServiceBilling.isHourly(order.provider.priceUnit) &&
                                order.status == Constant.orderOngoing &&
                                order.startTime != null)
                              ElapsedClock(
                                start: order.startTime!.toDate(),
                                end: order.endTime?.toDate(),
                                prefix: 'Tempo: ',
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.primary),
                              ),
                          ],
                        ),
                        isThreeLine: true,
                        trailing: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: _color(order.status).withValues(alpha: 0.12),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            Constant.statusLabel(order.status),
                            style: TextStyle(color: _color(order.status), fontSize: 12, fontWeight: FontWeight.w600),
                          ),
                        ),
                        onTap: () => Get.to(() => OrderDetailScreen(orderId: order.id)),
                      );
                    },
                  );
                },
              ),
            ),
          ],
        );
      }),
    );
  }
}
