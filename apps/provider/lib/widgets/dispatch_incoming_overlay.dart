import 'dart:async';

import 'package:arrow_shared/dispatch_offer.dart';
import 'package:arrow_shared/geo_distance.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/screens/order_detail_screen.dart';
import 'package:provider/service/broadcast_dispatch.dart';
import 'package:provider/service/dispatch_ringtone.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class DispatchIncomingScope extends StatelessWidget {
  const DispatchIncomingScope({super.key, required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return StreamBuilder<User?>(
      stream: FirebaseAuth.instance.authStateChanges(),
      builder: (context, authSnap) {
        final uid = authSnap.data?.uid ?? '';
        if (uid.isEmpty) return child;
        return StreamBuilder<ProviderOrderModel?>(
          stream: BroadcastDispatch.watchOffered(uid),
          builder: (context, offerSnap) {
            final order = offerSnap.data;
            return Stack(
              fit: StackFit.expand,
              children: [
                child,
                if (order != null) DispatchIncomingOverlay(order: order),
              ],
            );
          },
        );
      },
    );
  }
}

class DispatchIncomingOverlay extends StatefulWidget {
  const DispatchIncomingOverlay({super.key, required this.order});

  final ProviderOrderModel order;

  @override
  State<DispatchIncomingOverlay> createState() => _DispatchIncomingOverlayState();
}

class _DispatchIncomingOverlayState extends State<DispatchIncomingOverlay> {
  Timer? _timer;
  int _left = DispatchOffer.offerWindowSeconds;
  bool _busy = false;

  @override
  void initState() {
    super.initState();
    DispatchRingtone.start();
    _syncDeadline();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) => _tick());
  }

  @override
  void didUpdateWidget(covariant DispatchIncomingOverlay oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.order.id != widget.order.id || oldWidget.order.dispatchOfferExpiresAt != widget.order.dispatchOfferExpiresAt) {
      _syncDeadline();
      DispatchRingtone.start();
    }
  }

  void _syncDeadline() {
    final end = widget.order.dispatchOfferExpiresAt?.toDate() ?? DateTime.now().add(DispatchOffer.offerWindow);
    _left = end.difference(DateTime.now()).inSeconds;
    if (_left < 0) _left = 0;
  }

  Future<void> _tick() async {
    if (!mounted) return;
    setState(_syncDeadline);
    if (_left <= 0 && !_busy) {
      await _timeout();
    }
  }

  Future<void> _timeout() async {
    if (_busy) return;
    _busy = true;
    await DispatchRingtone.stop();
    try {
      await BroadcastDispatch.rejectAndAdvance(widget.order.id);
    } catch (_) {}
    if (mounted) _busy = false;
  }

  Future<void> _reject() async {
    if (_busy) return;
    _busy = true;
    await DispatchRingtone.stop();
    try {
      await BroadcastDispatch.rejectAndAdvance(widget.order.id);
    } catch (_) {}
    if (mounted) _busy = false;
  }

  Future<void> _accept() async {
    if (_busy) return;
    _busy = true;
    await DispatchRingtone.stop();
    final uid = FireStoreUtils.getCurrentUid();
    try {
      final services = await FireStoreUtils.getMyServices(uid);
      final user = await FireStoreUtils.getUserProfile(uid) ?? Constant.userModel;
      final service = FireStoreUtils.matchingService(services, widget.order);
      if (service == null || user == null) {
        ShowToastDialog.showToast('Publique um serviço nesta categoria para aceitar.');
        _busy = false;
        return;
      }
      await FireStoreUtils.acceptBroadcast(orderId: widget.order.id, service: service, providerUser: user);
      ShowToastDialog.showToast('Pedido aceito.');
      Get.to(() => OrderDetailScreen(orderId: widget.order.id));
    } on BroadcastTakenException {
      ShowToastDialog.showToast('Outro prestador já aceitou');
    } catch (e) {
      ShowToastDialog.showToast(e.toString());
    }
    if (mounted) _busy = false;
  }

  @override
  void dispose() {
    _timer?.cancel();
    DispatchRingtone.stop();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final order = widget.order;
    final user = Constant.userModel;
    final km = GeoDistance.formatKm(GeoDistance.km(
      fromLat: user?.latitude,
      fromLng: user?.longitude,
      toLat: order.customerLat(),
      toLng: order.customerLng(),
    ));
    return Material(
      color: const Color(0xF2000000),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 32, 24, 24),
          child: Column(
            children: [
              const Text(
                'Novo pedido próximo',
                style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 8),
              Text(
                '$_left s',
                style: const TextStyle(color: AppTheme.primary, fontSize: 40, fontWeight: FontWeight.w700),
              ),
              const Spacer(),
              Text(
                order.provider.title.isEmpty ? 'Serviço' : order.provider.title,
                textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 12),
              if (order.addressLine().isNotEmpty)
                Text(
                  order.addressLine(),
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.white70, fontSize: 16),
                ),
              if (km.isNotEmpty) ...[
                const SizedBox(height: 10),
                Text(km, style: const TextStyle(color: AppTheme.primary, fontSize: 18, fontWeight: FontWeight.w600)),
              ],
              const Spacer(),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _busy ? null : _reject,
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.white,
                        side: const BorderSide(color: Colors.white54),
                        minimumSize: const Size.fromHeight(52),
                      ),
                      child: const Text('Recusar'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: _busy ? null : _accept,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.success,
                        foregroundColor: Colors.white,
                        minimumSize: const Size.fromHeight(52),
                      ),
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
