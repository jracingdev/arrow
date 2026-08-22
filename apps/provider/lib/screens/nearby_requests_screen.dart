import 'package:arrow_shared/geo_distance.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/screens/order_detail_screen.dart';
import 'package:provider/service/broadcast_dispatch.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/utils/service_navigation.dart';

class NearbyRequestsScreen extends StatelessWidget {
  const NearbyRequestsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Pedidos próximos')),
      body: StreamBuilder<UserModel?>(
        stream: FireStoreUtils.watchUser(uid),
        builder: (context, userSnap) {
          final user = userSnap.data ?? Constant.userModel;
          return StreamBuilder<List<ProviderServiceModel>>(
            stream: FireStoreUtils.watchMyServices(uid),
            builder: (context, serviceSnap) {
              final services = serviceSnap.data ?? const <ProviderServiceModel>[];
              return StreamBuilder<List<ProviderOrderModel>>(
                stream: FireStoreUtils.watchNearbyBroadcast(
                  uid: uid,
                  myServices: services,
                  lat: user?.latitude,
                  lng: user?.longitude,
                  online: user?.online == true,
                ),
                builder: (context, orderSnap) {
                  final orders = orderSnap.data ?? const <ProviderOrderModel>[];
                  if (orderSnap.connectionState == ConnectionState.waiting && !orderSnap.hasData) {
                    return const Center(child: CircularProgressIndicator());
                  }
                  if (orders.isEmpty) {
                    return Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Text(
                          user?.online == true
                              ? 'Nenhum pedido próximo no momento.'
                              : 'Ative “Disponível para pedidos próximos” no início para ver chamados.',
                          textAlign: TextAlign.center,
                        ),
                      ),
                    );
                  }
                  return ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: orders.length,
                    separatorBuilder: (_, _) => const SizedBox(height: 10),
                    itemBuilder: (context, i) => NearbyRequestCard(
                      order: orders[i],
                      services: services,
                      user: user,
                    ),
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}

class NearbyRequestCard extends StatelessWidget {
  const NearbyRequestCard({
    super.key,
    required this.order,
    required this.services,
    required this.user,
  });

  final ProviderOrderModel order;
  final List<ProviderServiceModel> services;
  final UserModel? user;

  @override
  Widget build(BuildContext context) {
    final when = order.scheduleDateTime ?? order.createdAt;
    final date = when == null ? '' : DateFormat('dd/MM/yyyy HH:mm').format(when.toDate());
    final km = GeoDistance.formatKm(GeoDistance.km(
      fromLat: user?.latitude,
      fromLng: user?.longitude,
      toLat: order.customerLat(),
      toLng: order.customerLng(),
    ));
    return Material(
      color: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: AppTheme.grey200)),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    order.provider.title.isEmpty ? 'Serviço' : order.provider.title,
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
                if (km.isNotEmpty) Text(km, style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w600)),
              ],
            ),
            const SizedBox(height: 4),
            Text(order.author.fullName().isEmpty ? 'Cliente' : order.author.fullName()),
            if (order.addressLine().isNotEmpty) Text(order.addressLine(), style: const TextStyle(color: AppTheme.grey500, fontSize: 13)),
            if (date.isNotEmpty) Text(date, style: const TextStyle(color: AppTheme.grey500, fontSize: 13)),
            if (order.addressLine().isNotEmpty || order.customerLat() != null)
              Align(
                alignment: Alignment.centerLeft,
                child: TextButton.icon(
                  onPressed: () => ServiceNavigation.open(
                    name: order.provider.title,
                    latitude: order.customerLat(),
                    longitude: order.customerLng(),
                    address: order.addressLine(),
                  ),
                  icon: const Icon(Icons.directions, size: 18),
                  label: const Text('Como chegar'),
                ),
              ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () async {
                      try {
                        await BroadcastDispatch.rejectAndAdvance(order.id);
                      } catch (_) {}
                    },
                    style: OutlinedButton.styleFrom(foregroundColor: AppTheme.danger, minimumSize: const Size.fromHeight(40)),
                    child: const Text('Recusar'),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () => _accept(context),
                    style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(40)),
                    child: const Text('Aceitar'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _accept(BuildContext context) async {
    final service = FireStoreUtils.matchingService(services, order);
    final providerUser = user ?? Constant.userModel;
    if (service == null || providerUser == null) {
      ShowToastDialog.showToast('Publique um serviço nesta categoria para aceitar.');
      return;
    }
    try {
      await FireStoreUtils.acceptBroadcast(orderId: order.id, service: service, providerUser: providerUser);
      ShowToastDialog.showToast('Pedido aceito.');
      Get.to(() => OrderDetailScreen(orderId: order.id));
    } on BroadcastTakenException {
      ShowToastDialog.showToast('Outro prestador já aceitou');
    } catch (e) {
      ShowToastDialog.showToast(e.toString());
    }
  }
}
