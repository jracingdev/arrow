import 'dart:async';

import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:customer/constant/collection_name.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/models/onprovider_order_model.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_dashboard_screen.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_order_details_screen.dart';
import 'package:customer/screen_ui/on_demand_service/on_demand_payment_screen.dart';
import 'package:customer/service/fire_store_utils.dart';
import 'package:customer/themes/show_toast_dialog.dart';
import 'package:get/get.dart';

import 'on_demand_dashboard_controller.dart';

class OnDemandBroadcastWaitingController extends GetxController {
  Rxn<OnProviderOrderModel> order = Rxn<OnProviderOrderModel>();
  StreamSubscription? _sub;
  bool _handledAccept = false;

  @override
  void onInit() {
    super.onInit();
    if (Get.arguments is OnProviderOrderModel) {
      order.value = Get.arguments as OnProviderOrderModel;
    }
    _listen();
  }

  void _listen() {
    final id = order.value?.id;
    if (id == null || id.isEmpty) return;
    _sub = FireStoreUtils.fireStore.collection(CollectionName.providerOrders).doc(id).snapshots().listen((snap) {
      if (!snap.exists || snap.data() == null) return;
      final next = OnProviderOrderModel.fromJson(snap.data()!);
      order.value = next;
      if (next.status == Constant.orderCancelled) {
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
    final ok = await FireStoreUtils.cancelUnassignedBroadcast(current.id);
    ShowToastDialog.closeLoader();
    if (!ok) {
      ShowToastDialog.showToast("Outro prestador já aceitou".tr);
      return;
    }
    Get.offAll(const OnDemandDashboardScreen());
    Get.put(OnDemandDashboardController()).selectedIndex.value = 2;
  }

  @override
  void onClose() {
    _sub?.cancel();
    super.onClose();
  }
}
