import 'dart:async';

import 'package:arrow_shared/dispatch_offer.dart';
import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:customer/constant/collection_name.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/models/onprovider_order_model.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_dashboard_screen.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_order_details_screen.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_payment_screen.dart';
import 'package:customer/service/broadcast_dispatch.dart';
import 'package:customer/service/fire_store_utils.dart';
import 'package:customer/service/send_notification.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:get/get.dart';

import 'on_demand_dashboard_controller.dart';

class OnDemandBroadcastWaitingController extends GetxController {
  Rxn<OnProviderOrderModel> order = Rxn<OnProviderOrderModel>();
  RxInt remainingSeconds = 0.obs;
  RxBool noProvider = false.obs;
  StreamSubscription? _sub;
  Timer? _tick;
  bool _handledAccept = false;
  bool _advancing = false;
  bool _expiring = false;
  String? _lastOfferedTo;

  @override
  void onInit() {
    super.onInit();
    if (Get.arguments is OnProviderOrderModel) {
      order.value = Get.arguments as OnProviderOrderModel;
    }
    _refreshCountdown();
    _listen();
    _tick = Timer.periodic(const Duration(seconds: 1), (_) => _onTick());
  }

  void _listen() {
    final id = order.value?.id;
    if (id == null || id.isEmpty) return;
    _sub = FireStoreUtils.fireStore.collection(CollectionName.providerOrders).doc(id).snapshots().listen((snap) {
      if (!snap.exists || snap.data() == null) return;
      final next = OnProviderOrderModel.fromJson(snap.data()!);
      order.value = next;
      _refreshCountdown();
      if (next.status == Constant.orderCancelled) {
        if (next.cancelReason == DispatchOffer.cancelReasonNoProvider) {
          noProvider.value = true;
          return;
        }
        ShowToastDialog.showToast("Pedido cancelado".tr);
        Get.offAll(const OnDemandDashboardScreen());
        return;
      }
      if (!_handledAccept && next.hasAssignedProvider) {
        _handledAccept = true;
        _onAccepted(next);
      }
    });
  }

  void _refreshCountdown() {
    final current = order.value;
    if (current == null) return;
    remainingSeconds.value = DispatchOffer.remainingJobSeconds(
      createdAt: current.createdAt.toDate(),
      dispatchExpiresAt: current.dispatchExpiresAt?.toDate(),
      now: DateTime.now(),
    );
  }

  Future<void> _onTick() async {
    final current = order.value;
    if (current == null || noProvider.value || _handledAccept) return;
    _refreshCountdown();
    final now = DateTime.now();
    if (DispatchOffer.isJobExpired(
      createdAt: current.createdAt.toDate(),
      dispatchExpiresAt: current.dispatchExpiresAt?.toDate(),
      now: now,
    )) {
      await _expireNoProvider();
      return;
    }
    if (DispatchOffer.shouldAdvanceOffer(
      status: current.status,
      assignedAuthor: current.provider.author,
      offeredTo: current.dispatchOfferedTo,
      offerExpiresAt: current.dispatchOfferExpiresAt?.toDate(),
      createdAt: current.createdAt.toDate(),
      dispatchExpiresAt: current.dispatchExpiresAt?.toDate(),
      now: now,
    )) {
      await _advance();
    }
  }

  Future<void> _expireNoProvider() async {
    if (_expiring || noProvider.value) return;
    _expiring = true;
    final current = order.value;
    if (current == null || current.id.isEmpty) {
      _expiring = false;
      return;
    }
    final ok = await BroadcastDispatch.cancelUnassigned(
      orderId: current.id,
      reason: 'Nenhum prestador disponível',
      cancelReason: DispatchOffer.cancelReasonNoProvider,
    );
    if (ok) {
      noProvider.value = true;
      final token = Constant.userModel?.fcmToken;
      if (token != null && token.isNotEmpty) {
        await SendNotification.sendOneNotification(
          token: token,
          title: 'Nenhum prestador disponível',
          body: 'Ninguém aceitou seu pedido em 10 minutos.',
          payload: {'type': DispatchOffer.fcmTypeOrder, 'orderId': current.id},
        );
      }
    }
    _expiring = false;
  }

  Future<void> _advance() async {
    if (_advancing) return;
    _advancing = true;
    try {
      final current = order.value;
      if (current == null) return;
      final next = await BroadcastDispatch.advance(current.id);
      if (next != null && next.uid != _lastOfferedTo && (next.fcmToken ?? '').isNotEmpty) {
        _lastOfferedTo = next.uid;
        await SendNotification.sendOneNotification(
          token: next.fcmToken!,
          title: 'Novo pedido próximo',
          body: '${current.provider.title ?? 'Serviço'} · ${current.address?.getFullAddress() ?? ''}'.trim(),
          payload: {'type': DispatchOffer.fcmTypeOffer, 'orderId': current.id},
        );
      }
    } catch (_) {
    } finally {
      _advancing = false;
    }
  }

  Future<void> _onAccepted(OnProviderOrderModel next) async {
    ShowToastDialog.showToast("${'Prestador encontrado'.tr}: ${next.provider.authorName ?? ''}");
    if (!HourlyServiceBilling.isHourly(next.provider.priceUnit) && next.paymentStatus != true) {
      final total = _estimateFixedTotal(next);
      Get.off(() => OnDemandPaymentScreen(), arguments: {
        'onDemandOrderModel': Rxn<OnProviderOrderModel>(next),
        'totalAmount': total,
        'isExtra': false,
      });
      return;
    }
    Get.offAll(const OnDemandDashboardScreen());
    Get.put(OnDemandDashboardController()).selectedIndex.value = 2;
    Get.to(() => const OnDemandOrderDetailsScreen(), arguments: next);
  }

  double _estimateFixedTotal(OnProviderOrderModel next) {
    final qty = next.quantity <= 0 ? 1.0 : next.quantity;
    final unit = (next.provider.disPrice == '' || next.provider.disPrice == '0')
        ? double.tryParse(next.provider.price ?? '0') ?? 0
        : double.tryParse(next.provider.disPrice ?? '0') ?? 0;
    var sub = unit * qty;
    var tax = 0.0;
    for (final taxElement in next.taxModel ?? Constant.orderProductTaxList ?? []) {
      tax += Constant.calculateTax(amount: sub.toString(), taxModel: taxElement);
    }
    final fee = double.tryParse(next.platformFee ?? Constant.platformFeeModel?.fee ?? '0') ?? 0;
    if (fee > 0 && Constant.platformFeeModel?.enable == true) {
      for (final taxElement in next.platformTax ?? Constant.platformTaxList ?? []) {
        tax += Constant.calculateTax(amount: fee.toString(), taxModel: taxElement);
      }
    }
    return sub + fee + tax;
  }

  Future<void> cancelSearch() async {
    final current = order.value;
    if (current == null || current.id.isEmpty) return;
    ShowToastDialog.showLoader("Please wait...".tr);
    final ok = await BroadcastDispatch.cancelUnassigned(orderId: current.id);
    ShowToastDialog.closeLoader();
    if (!ok) {
      ShowToastDialog.showToast("Outro prestador já aceitou".tr);
      return;
    }
    Get.offAll(const OnDemandDashboardScreen());
    Get.put(OnDemandDashboardController()).selectedIndex.value = 2;
  }

  void leave() {
    Get.offAll(const OnDemandDashboardScreen());
    Get.put(OnDemandDashboardController()).selectedIndex.value = 2;
  }

  String countdownLabel() {
    final total = remainingSeconds.value;
    final m = (total ~/ 60).toString().padLeft(2, '0');
    final s = (total % 60).toString().padLeft(2, '0');
    return '$m:$s';
  }

  @override
  void onClose() {
    _sub?.cancel();
    _tick?.cancel();
    super.onClose();
  }
}
