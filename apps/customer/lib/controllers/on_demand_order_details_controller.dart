import 'dart:async';
import 'dart:developer';
import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/models/coupon_model.dart';
import 'package:customer/models/user_model.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constant/collection_name.dart';
import '../constant/constant.dart';
import '../models/onprovider_order_model.dart';
import '../models/wallet_transaction_model.dart';
import '../models/worker_model.dart';
import '../service/fire_store_utils.dart';
import '../service/send_notification.dart';
import '../themes/show_toast_dialog.dart';
import '../utils/utils.dart';

class OnDemandOrderDetailsController extends GetxController {
  Rx<UserModel?> providerUser = Rx<UserModel?>(null);
  Rxn<OnProviderOrderModel> onProviderOrder = Rxn<OnProviderOrderModel>();
  Rxn<WorkerModel> worker = Rxn<WorkerModel>();

  Rx<TextEditingController> couponTextController = TextEditingController().obs;
  Rx<TextEditingController> cancelBookingController = TextEditingController().obs;

  RxDouble subTotal = 0.0.obs;
  RxDouble price = 0.0.obs;
  RxDouble discountAmount = 0.0.obs;
  RxDouble totalAmount = 0.0.obs;
  RxDouble quantity = 0.0.obs;

  RxString discountType = "".obs;
  RxString discountLabel = "".obs;
  RxString offerCode = "".obs;
  RxDouble orderTaxAmount = 0.0.obs;
  RxDouble platformTaxAmount = 0.0.obs;
  RxDouble taxAmount = 0.0.obs;

  RxList<CouponModel> couponList = <CouponModel>[].obs;

  final RxBool isLoading = false.obs;
  StreamSubscription? _invoiceSub;
  StreamSubscription? _providerLocSub;

  @override
  void onInit() {
    super.onInit();

    final args = Get.arguments;
    if (args != null && args is OnProviderOrderModel) {
      onProviderOrder.value = args;
    }
    getData();
    _watchOrder();
    _watchProviderLocation();
  }

  void _watchOrder() {
    final id = onProviderOrder.value?.id;
    if (id == null || id.isEmpty) return;
    _invoiceSub = FireStoreUtils.fireStore.collection(CollectionName.providerOrders).doc(id).snapshots().listen((snap) {
      if (!snap.exists || snap.data() == null) return;
      final next = OnProviderOrderModel.fromJson(snap.data()!);
      onProviderOrder.value = next;
      calculatePrice();
      final authorId = next.provider.author ?? '';
      if (authorId.isNotEmpty && providerUser.value?.id != authorId) {
        FireStoreUtils.getUserProfile(authorId).then((user) {
          if (user != null) providerUser.value = user;
        });
      }
    });
  }

  void _watchProviderLocation() {
    final id = onProviderOrder.value?.provider.author;
    if (id == null || id.isEmpty) return;
    _providerLocSub = FireStoreUtils.fireStore.collection(CollectionName.users).doc(id).snapshots().listen((snap) {
      if (!snap.exists || snap.data() == null) return;
      providerUser.value = UserModel.fromJson(snap.data()!);
    });
  }

  @override
  void onClose() {
    _invoiceSub?.cancel();
    _providerLocSub?.cancel();
    super.onClose();
  }

  Future<void> getData() async {
    try {
      final order = await FireStoreUtils.getProviderOrderById(onProviderOrder.value!.id);
      if (order != null) {
        onProviderOrder.value = order;

        discountType.value = order.discountType ?? "";
        discountLabel.value = order.discountLabel ?? "";
        discountAmount.value = double.tryParse(order.discount.toString()) ?? 0.0;
        offerCode.value = order.couponCode ?? "";

        // Fetch provider
        providerUser.value = await FireStoreUtils.getUserProfile(order.provider.author.toString());

        // Fetch worker (if exists)
        if (order.workerId != null && order.workerId!.isNotEmpty) {
          worker.value = await FireStoreUtils.getWorker(order.workerId!);
        } else {
          worker.value = null;
        }

        calculatePrice();

        _providerLocSub?.cancel();
        _watchProviderLocation();

        // Load available coupons
        FireStoreUtils.getProviderCouponAfterExpire(order.provider.author.toString()).then((expiredCoupons) {
          couponList.assignAll(expiredCoupons);
        });
      } else {
        onProviderOrder.value = null;
        providerUser.value = null;
        worker.value = null;
        couponList.clear();
      }
    } catch (e, st) {
      log("Error in getData: $e\n$st");
      onProviderOrder.value = null;
      providerUser.value = null;
      worker.value = null;
      couponList.clear();
    }
  }

  void applyCoupon(CouponModel coupon) {
    double discount = 0.0;
    if (coupon.discountType == "Percentage" || coupon.discountType == "Percent") {
      discount = price.value * (double.tryParse(coupon.discount.toString()) ?? 0) / 100;
    } else {
      discount = double.tryParse(coupon.discount.toString()) ?? 0;
    }

    if (subTotal.value > discount) {
      discountType.value = coupon.discountType ?? '';
      discountLabel.value = coupon.discount.toString();
      offerCode.value = coupon.code ?? '';
      calculatePrice();
    } else {
      Get.snackbar("Error".tr, "Coupon cannot be applied".tr);
    }
  }

  void calculatePrice() {
    orderTaxAmount.value = 0.0;
    platformTaxAmount.value = 0.0;
    taxAmount.value = 0.0;
    subTotal.value = 0.0;
    totalAmount.value = 0.0;
    double basePrice =
        (onProviderOrder.value?.provider.disPrice == "" || onProviderOrder.value?.provider.disPrice == "0")
            ? double.tryParse(onProviderOrder.value?.provider.price.toString() ?? "0") ?? 0
            : double.tryParse(onProviderOrder.value?.provider.disPrice.toString() ?? "0") ?? 0;

    var qty = onProviderOrder.value?.quantity ?? 0.0;
    if (HourlyServiceBilling.isHourly(onProviderOrder.value?.provider.priceUnit) && qty < 1) {
      qty = 1;
    }
    price.value = basePrice * qty;

    // discount
    if (discountType.value == "Percentage" || discountType.value == "Percent") {
      discountAmount.value = price.value * (double.tryParse(discountLabel.value) ?? 0) / 100;
    } else {
      discountAmount.value = double.tryParse(discountLabel.value.isEmpty ? '0' : discountLabel.value) ?? 0;
    }

    subTotal.value = price.value - discountAmount.value;

    for (var taxElement in onProviderOrder.value?.taxModel ?? []) {
      orderTaxAmount.value += Constant.calculateTax(amount: (subTotal.value).toString(), taxModel: taxElement);
    }

    if (double.parse(onProviderOrder.value?.platformFee ?? '0.0') > 0.0) {
      for (var taxElement in onProviderOrder.value?.platformTax ?? []) {
        platformTaxAmount.value += Constant.calculateTax(amount: onProviderOrder.value?.platformFee ?? '0.0', taxModel: taxElement);
      }
    }

    taxAmount.value = orderTaxAmount.value + platformTaxAmount.value;

    totalAmount.value = (subTotal.value) + double.parse(onProviderOrder.value?.platformFee ?? '0.0') + taxAmount.value;
  }

  String getDate(String date) {
    try {
      DateTime dt = DateTime.parse(date);
      return "${dt.day}-${dt.month}-${dt.year}";
    } catch (e) {
      return date;
    }
  }

  Future<void> cancelBooking() async {
    final order = onProviderOrder.value;
    if (order == null) return;

    ShowToastDialog.showLoader("Please wait...".tr);

    try {
      double price = 0.0;
      double discountAmount = 0.0;
      double orderTaxAmount = 0.0;
      double platformTaxAmount = 0.0;
      double taxAmount = 0.0;
      double totalAmount = 0.0;

      // Calculate total
      final pricePerUnit =
          (order.provider.disPrice == "" || order.provider.disPrice == "0") ? double.tryParse(order.provider.price.toString()) ?? 0 : double.tryParse(order.provider.disPrice.toString()) ?? 0;

      price = pricePerUnit * (order.quantity);

      if (discountType.value == "Percentage" || discountType.value == "Percent") {
        discountAmount = price * (double.tryParse(discountLabel.value) ?? 0) / 100;
      } else {
        discountAmount = double.tryParse(discountLabel.value.isEmpty ? '0' : discountLabel.value) ?? 0;
      }

      // Add tax
      for (var taxElement in order.taxModel ?? []) {
        orderTaxAmount += Constant.calculateTax(amount: (price - discountAmount).toString(), taxModel: taxElement);
      }
      if (double.parse(order.platformFee ?? '0.0') > 0.0) {
        for (var taxElement in Constant.platformTaxList ?? []) {
          platformTaxAmount += Constant.calculateTax(amount: order.platformFee ?? '0.0', taxModel: taxElement);
        }
      }
      taxAmount = orderTaxAmount + platformTaxAmount;

      totalAmount = (price - discountAmount) + double.parse(order.platformFee ?? '0.0') + taxAmount;

      // Admin commission
      double adminComm = 0.0;
      if ((order.adminCommission ?? '0') != '0' && (order.adminCommissionType ?? '').isNotEmpty) {
        if (order.adminCommissionType!.toLowerCase() == 'percentage' || order.adminCommissionType!.toLowerCase() == 'percent') {
          adminComm = (totalAmount * (double.tryParse(order.adminCommission!) ?? 0)) / 100;
        } else {
          adminComm = double.tryParse(order.adminCommission!) ?? 0;
        }
      }
      final provider = await FireStoreUtils.getUserProfile(order.provider.author ?? '');
      // Refund customer wallet if not COD
      if ((order.payment_method).toLowerCase() != 'cod') {
        await FireStoreUtils.setWalletTransaction(
          WalletTransactionModel(
            id: Constant.getUuid(),
            serviceType: 'ondemand-service',
            amount: totalAmount,
            date: Timestamp.now(),
            paymentMethod: 'wallet',
            transactionUser: 'customer',
            userId: Constant.userModel?.id,
            isTopup: true,
            orderId: order.id,
            note: 'Booking Amount Refund',
            paymentStatus: "success",
          ),
        );

        // Deduct from provider if accepted
        if (order.status == Constant.orderAccepted) {
          await FireStoreUtils.setWalletTransaction(
            WalletTransactionModel(
              id: Constant.getUuid(),
              serviceType: 'ondemand-service',
              amount: totalAmount,
              date: Timestamp.now(),
              paymentMethod: 'wallet',
              transactionUser: 'provider',
              userId: order.provider.author ?? '',
              isTopup: false,
              orderId: order.id,
              note: 'Booking Amount Refund',
              paymentStatus: "success",
            ),
          );
        }
        await FireStoreUtils.updateUserWallet(amount: totalAmount.toString(), userId: FireStoreUtils.getCurrentUid());
      }

      // Refund admin commission
      if (order.status == Constant.orderAccepted && adminComm > 0) {
        await FireStoreUtils.setWalletTransaction(
          WalletTransactionModel(
            id: Constant.getUuid(),
            serviceType: 'ondemand-service',
            amount: adminComm,
            date: Timestamp.now(),
            paymentMethod: 'wallet',
            transactionUser: 'provider',
            userId: order.provider.author ?? '',
            isTopup: true,
            orderId: order.id,
            note: 'Admin commission refund',
            paymentStatus: "success",
          ),
        );
      }

      // Update order status & reason
      order.status = Constant.orderCancelled;
      order.reason = cancelBookingController.value.text;

      await FireStoreUtils.updateOnDemandOrder(order); // Ensure this completes

      // Notify provider

      if (provider != null) {
        Map<String, dynamic> payload = {"type": 'provider_order', "orderId": order.id};
        await SendNotification.sendFcmMessage(Constant.bookingPlaced, provider.fcmToken ?? '', payload);
      }

      ShowToastDialog.closeLoader();
      Get.back();
      ShowToastDialog.showToast("Booking cancelled successfully".tr);
    } catch (e, st) {
      log("Cancel error: $e\n$st");
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast("Something went wrong".tr);
    }
  }

  Future<void> openInvoice(String url) async {
    final uri = Uri.tryParse(url);
    if (uri == null || !await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      ShowToastDialog.showToast('Não foi possível abrir a nota fiscal.'.tr);
    }
  }

  bool canReport(String? status) {
    return status == Constant.orderAssigned ||
        status == Constant.orderOngoing ||
        status == Constant.orderCompleted ||
        status == Constant.orderCancelled ||
        status == Constant.orderAccepted ||
        status == Constant.orderInTransit;
  }

  bool canSos(String? status) {
    return status == Constant.orderOngoing || status == Constant.orderAssigned || status == Constant.orderInTransit;
  }

  Future<void> submitReport({required String category, required String description, bool sos = false}) async {
    final order = onProviderOrder.value;
    if (order == null) return;
    final reportedId = order.provider.author ?? '';
    ShowToastDialog.showLoader("Please wait...".tr);
    try {
      if (sos) {
        double? lat;
        double? lng;
        try {
          final pos = await Utils.getCurrentLocation();
          lat = pos?.latitude;
          lng = pos?.longitude;
        } catch (_) {}
        await FireStoreUtils.setOnDemandSos(
          orderId: order.id,
          reporterId: FireStoreUtils.getCurrentUid(),
          latitude: lat,
          longitude: lng,
        );
      }
      await FireStoreUtils.setOnDemandComplaint(
        orderId: order.id,
        reporterId: FireStoreUtils.getCurrentUid(),
        reporterRole: 'customer',
        reporterName: Constant.userModel?.fullName() ?? '',
        reportedId: reportedId,
        reportedRole: 'provider',
        reportedName: providerUser.value?.fullName() ?? order.provider.authorName ?? '',
        category: category,
        description: description,
        priority: sos ? 'high' : 'normal',
      );
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('A plataforma vai analisar sua denúncia.'.tr);
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString().tr);
    }
  }
}
