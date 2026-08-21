import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:arrow_shared/rating_average.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/provider_document_model.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/screens/documents_screen.dart';
import 'package:provider/screens/home_shell_controller.dart';
import 'package:provider/screens/nearby_requests_screen.dart';
import 'package:provider/screens/order_detail_screen.dart';
import 'package:provider/screens/ratings_screen.dart';
import 'package:provider/screens/services_screen.dart';
import 'package:provider/screens/wallet_screen.dart';
import 'package:provider/screens/workers_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/widgets/elapsed_clock.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  String _greeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'Bom dia';
    if (hour < 18) return 'Boa tarde';
    return 'Boa noite';
  }

  bool _isToday(DateTime? date) {
    if (date == null) return false;
    final now = DateTime.now();
    return date.year == now.year && date.month == now.month && date.day == now.day;
  }

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      body: StreamBuilder<UserModel?>(
        stream: FireStoreUtils.watchUser(uid),
        builder: (context, userSnap) {
          final user = userSnap.data ?? Constant.userModel;
          if (user != null) Constant.userModel = user;
          final verified = user?.isDocumentVerify == true;
          final first = (user?.firstName ?? '').trim();
          return StreamBuilder<List<ProviderOrderModel>>(
            stream: FireStoreUtils.watchMyOrders(uid),
            builder: (context, orderSnap) {
              final orders = orderSnap.data ?? const <ProviderOrderModel>[];
              final novos = orders.where((o) => o.status == Constant.orderPlaced).toList();
              final andamento = orders.where((o) => Constant.tabOngoing.contains(o.status)).toList();
              final concluidosHoje = orders.where((o) {
                if (o.status != Constant.orderCompleted) return false;
                final when = o.endTime ?? o.createdAt;
                return _isToday(when?.toDate());
              }).length;
              final upcoming = orders.where((o) => Constant.tabUpcoming.contains(o.status)).toList()
                ..sort((a, b) {
                  final da = (a.newScheduleDateTime ?? a.scheduleDateTime)?.millisecondsSinceEpoch ?? 0;
                  final db = (b.newScheduleDateTime ?? b.scheduleDateTime)?.millisecondsSinceEpoch ?? 0;
                  return da.compareTo(db);
                });
              final wallet = user?.walletAmount ?? 0;
              final avg = RatingAverage.formatted(user?.reviewsSum, user?.reviewsCount);

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
                    ),
                    builder: (context, nearbySnap) {
                      final nearby = nearbySnap.data ?? const <ProviderOrderModel>[];
                      return CustomScrollView(
                slivers: [
                  SliverToBoxAdapter(
                    child: Container(
                      color: const Color(0xFF111827),
                      padding: EdgeInsets.fromLTRB(20, MediaQuery.of(context).padding.top + 16, 20, 24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(_greeting(), style: const TextStyle(color: Colors.white70, fontSize: 14)),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  first.isEmpty ? 'Prestador' : first,
                                  style: const TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.w700),
                                ),
                              ),
                              StreamBuilder<ProviderDocumentModel?>(
                                stream: FireStoreUtils.watchDocumentsVerify(uid),
                                builder: (context, verifySnap) {
                                  final rejected = verified != true && (verifySnap.data?.hasRejected == true);
                                  return _VerifyBadge(verified: verified, rejected: rejected);
                                },
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            'Nota $avg · ${user?.reviewsCount ?? 0} avaliações',
                            style: const TextStyle(color: Colors.white70, fontSize: 13),
                          ),
                        ],
                      ),
                    ),
                  ),
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                    sliver: SliverToBoxAdapter(
                      child: GridView.count(
                        crossAxisCount: 2,
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        mainAxisSpacing: 10,
                        crossAxisSpacing: 10,
                        childAspectRatio: 1.55,
                        children: [
                          _StatCard(
                            label: 'Novos pedidos',
                            value: '${novos.length}',
                            icon: Icons.fiber_new_outlined,
                            color: AppTheme.warning,
                            onTap: () => Get.find<HomeShellController>().openOrders(tab: 1),
                          ),
                          _StatCard(
                            label: 'Em andamento',
                            value: '${andamento.length}',
                            icon: Icons.timelapse,
                            color: const Color(0xFF7C3AED),
                            onTap: () => Get.find<HomeShellController>().openOrders(tab: 3),
                          ),
                          _StatCard(
                            label: 'Concluídos hoje',
                            value: '$concluidosHoje',
                            icon: Icons.check_circle_outline,
                            color: AppTheme.success,
                            onTap: () => Get.find<HomeShellController>().openOrders(tab: 4),
                          ),
                          _StatCard(
                            label: 'Carteira',
                            value: 'R\$ ${wallet.toStringAsFixed(2).replaceAll('.', ',')}',
                            icon: Icons.account_balance_wallet_outlined,
                            color: AppTheme.primary,
                            onTap: () => Get.to(() => const WalletScreen()),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SliverToBoxAdapter(
                    child: Padding(
                      padding: EdgeInsets.fromLTRB(16, 8, 16, 8),
                      child: Text('Atalhos', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                    ),
                  ),
                  SliverToBoxAdapter(
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Row(
                        children: [
                          _QuickAction(
                            icon: Icons.near_me_outlined,
                            label: 'Próximos',
                            onTap: () => Get.to(() => const NearbyRequestsScreen()),
                          ),
                          _QuickAction(
                            icon: Icons.calendar_today_outlined,
                            label: 'Agenda',
                            onTap: () => Get.find<HomeShellController>().goTo(2),
                          ),
                          _QuickAction(
                            icon: Icons.handyman_outlined,
                            label: 'Serviços',
                            onTap: () => Get.to(() => const ServicesScreen()),
                          ),
                          _QuickAction(
                            icon: Icons.groups_outlined,
                            label: 'Equipe',
                            onTap: () => Get.to(() => const WorkersScreen()),
                          ),
                          _QuickAction(
                            icon: Icons.account_balance_wallet_outlined,
                            label: 'Carteira',
                            onTap: () => Get.to(() => const WalletScreen()),
                          ),
                          _QuickAction(
                            icon: Icons.star_outline,
                            label: 'Avaliações',
                            onTap: () => Get.to(() => const RatingsScreen()),
                          ),
                          _QuickAction(
                            icon: Icons.badge_outlined,
                            label: 'Documentos',
                            onTap: () => Get.to(() => const DocumentsScreen()),
                          ),
                        ],
                      ),
                    ),
                  ),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                      child: Row(
                        children: [
                          const Expanded(
                            child: Text('Próximos na agenda', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          ),
                          TextButton(
                            onPressed: () => Get.find<HomeShellController>().goTo(2),
                            child: const Text('Ver agenda'),
                          ),
                        ],
                      ),
                    ),
                  ),
                  if (upcoming.isEmpty)
                    const SliverToBoxAdapter(
                      child: Padding(
                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        child: Text('Nenhum atendimento agendado.', style: TextStyle(color: AppTheme.grey500)),
                      ),
                    )
                  else
                    SliverList.separated(
                      itemCount: upcoming.take(3).length,
                      separatorBuilder: (_, _) => const SizedBox(height: 8),
                      itemBuilder: (context, i) => Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: _UpcomingTile(order: upcoming[i]),
                      ),
                    ),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                      child: Row(
                        children: [
                          const Expanded(
                            child: Text('Pedidos próximos', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          ),
                          TextButton(
                            onPressed: () => Get.to(() => const NearbyRequestsScreen()),
                            child: Text(nearby.isEmpty ? 'Ver inbox' : 'Ver todos (${nearby.length})'),
                          ),
                        ],
                      ),
                    ),
                  ),
                  if (nearby.isEmpty)
                    const SliverToBoxAdapter(
                      child: Padding(
                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        child: Text('Nenhum chamado próximo no momento.', style: TextStyle(color: AppTheme.grey500)),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
                      sliver: SliverList.separated(
                        itemCount: nearby.take(3).length,
                        separatorBuilder: (_, _) => const SizedBox(height: 10),
                        itemBuilder: (context, i) => NearbyRequestCard(order: nearby[i], services: services, user: user),
                      ),
                    ),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                      child: Row(
                        children: [
                          const Expanded(
                            child: Text('Pedidos para aceitar', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          ),
                          TextButton(
                            onPressed: () => Get.find<HomeShellController>().openOrders(tab: 1),
                            child: const Text('Ver todos'),
                          ),
                        ],
                      ),
                    ),
                  ),
                  if (novos.isEmpty)
                    const SliverToBoxAdapter(
                      child: Padding(
                        padding: EdgeInsets.fromLTRB(16, 8, 16, 32),
                        child: Text('Nenhum pedido novo no momento.', style: TextStyle(color: AppTheme.grey500)),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 0, 16, 32),
                      sliver: SliverList.separated(
                        itemCount: novos.take(5).length,
                        separatorBuilder: (_, _) => const SizedBox(height: 10),
                        itemBuilder: (context, i) => _RequestCard(order: novos[i]),
                      ),
                    ),
                ],
              );
                    },
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

class _VerifyBadge extends StatelessWidget {
  const _VerifyBadge({required this.verified, this.rejected = false});

  final bool verified;
  final bool rejected;

  @override
  Widget build(BuildContext context) {
    final color = verified ? AppTheme.success : (rejected ? const Color(0xFFB91C1C) : AppTheme.warning);
    final label = verified ? 'Verificado' : (rejected ? 'Recusado' : 'Pendente');
    final icon = verified ? Icons.verified : (rejected ? Icons.cancel_outlined : Icons.hourglass_top);
    return InkWell(
      onTap: verified ? null : () => Get.to(() => const DocumentsScreen()),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.18),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(width: 6),
            Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w600, fontSize: 12)),
          ],
        ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  const _StatCard({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  final String label;
  final String value;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppTheme.grey50,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(icon, color: color, size: 22),
              const Spacer(),
              Text(value, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              Text(label, style: const TextStyle(color: AppTheme.grey500, fontSize: 12)),
            ],
          ),
        ),
      ),
    );
  }
}

class _QuickAction extends StatelessWidget {
  const _QuickAction({required this.icon, required this.label, required this.onTap});

  final IconData icon;
  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(right: 10),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          width: 88,
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            border: Border.all(color: AppTheme.grey200),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Column(
            children: [
              Icon(icon, color: AppTheme.primary),
              const SizedBox(height: 6),
              Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
            ],
          ),
        ),
      ),
    );
  }
}

class _UpcomingTile extends StatelessWidget {
  const _UpcomingTile({required this.order});

  final ProviderOrderModel order;

  @override
  Widget build(BuildContext context) {
    final when = order.newScheduleDateTime ?? order.scheduleDateTime;
    final date = when == null ? 'Horário a confirmar' : DateFormat('dd/MM HH:mm').format(when.toDate());
    return ListTile(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: AppTheme.grey200)),
      leading: const CircleAvatar(
        backgroundColor: Color(0x1A00A1F1),
        child: Icon(Icons.event_outlined, color: AppTheme.primary),
      ),
      title: Text(order.provider.title.isEmpty ? 'Serviço' : order.provider.title),
      subtitle: Text('$date · ${order.author.fullName()}'),
      trailing: const Icon(Icons.chevron_right),
      onTap: () => Get.to(() => OrderDetailScreen(orderId: order.id)),
    );
  }
}

class _RequestCard extends StatelessWidget {
  const _RequestCard({required this.order});

  final ProviderOrderModel order;

  @override
  Widget build(BuildContext context) {
    final when = order.newScheduleDateTime ?? order.scheduleDateTime ?? order.createdAt;
    final date = when == null ? '' : DateFormat('dd/MM/yyyy HH:mm').format(when.toDate());
    final hourly = HourlyServiceBilling.isHourly(order.provider.priceUnit);
    return Material(
      color: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: AppTheme.grey200)),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => Get.to(() => OrderDetailScreen(orderId: order.id)),
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
                  Text(hourly ? 'Por hora' : 'Preço fixo', style: const TextStyle(fontSize: 12, color: AppTheme.grey500)),
                ],
              ),
              const SizedBox(height: 4),
              Text(order.author.fullName()),
              if (date.isNotEmpty) Text(date, style: const TextStyle(color: AppTheme.grey500, fontSize: 13)),
              if (HourlyServiceBilling.isHourly(order.provider.priceUnit) &&
                  order.status == Constant.orderOngoing &&
                  order.startTime != null)
                ElapsedClock(
                  start: order.startTime!.toDate(),
                  end: order.endTime?.toDate(),
                  prefix: 'Tempo: ',
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.primary),
                ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () async {
                        try {
                          await FireStoreUtils.updateOrder(order.id, {'status': Constant.orderRejected});
                        } catch (_) {}
                      },
                      style: OutlinedButton.styleFrom(foregroundColor: AppTheme.danger, minimumSize: const Size.fromHeight(40)),
                      child: const Text('Recusar'),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () async {
                        try {
                          await FireStoreUtils.updateOrder(order.id, {
                            'status': Constant.orderAccepted,
                            'newScheduleDateTime': order.scheduleDateTime,
                          });
                        } catch (_) {}
                      },
                      style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(40)),
                      child: const Text('Aceitar'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
