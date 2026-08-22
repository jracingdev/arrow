import 'package:arrow_shared/dispatch_offer.dart';
import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/models/coupon_model.dart';
import 'package:customer/models/user_model.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import '../models/onprovider_order_model.dart';
import '../models/provider_serivce_model.dart';
import '../screen_ui/on_demand_service/on_demand_broadcast_waiting_screen.dart';
import '../screen_ui/on_demand_service/on_demand_dashboard_screen.dart';
import '../screen_ui/on_demand_service/on_demand_payment_screen.dart';
import '../service/broadcast_dispatch.dart';
import '../service/fire_store_utils.dart';
import '../service/send_notification.dart';
import '../themes/show_toast_dialog.dart';
import 'on_demand_dashboard_controller.dart';

class OnDemandBookingController extends GetxController {
  Rxn<ProviderServiceModel> provider = Rxn<ProviderServiceModel>();
  RxString categoryTitle = ''.obs;
  RxString categoryId = ''.obs;
  RxBool isBroadcast = false.obs;

  RxInt quantity = 1.obs;
  Rx<TextEditingController> descriptionController = TextEditingController().obs;
  Rx<TextEditingController> dateTimeController = TextEditingController().obs;
  Rx<TextEditingController> couponTextController = TextEditingController().obs;

  Rx<DateTime> selectedDateTime = DateTime.now().obs;
  RxString dateTimeText = "".obs;

  RxList<CouponModel> couponList = <CouponModel>[].obs;

  RxDouble subTotal = 0.0.obs;
  RxDouble price = 0.0.obs;
  RxDouble discountAmount = 0.0.obs;
  RxDouble totalAmount = 0.0.obs;
  RxDouble orderTaxAmount = 0.0.obs;
  RxDouble platformTaxAmount = 0.0.obs;
  RxDouble taxAmount = 0.0.obs;

  RxString discountType = "".obs;
  RxString discountLabel = "".obs;
  RxString offerCode = "".obs;

  Rx<ShippingAddress> selectedAddress = ShippingAddress().obs;

  @override
  void onInit() {
    super.onInit();
    final raw = Get.arguments;
    final Map<String, dynamic>? args = raw is Map ? Map<String, dynamic>.from(raw) : null;
    if (args != null) {
      isBroadcast.value = args['broadcast'] == true;
      categoryTitle.value = args['categoryTitle'] ?? '';
      categoryId.value = args['categoryId']?.toString() ?? '';
      if (args['providerModel'] is ProviderServiceModel) {
        provider.value = args['providerModel'] as ProviderServiceModel;
      }
      if (isBroadcast.value && (provider.value == null || (provider.value?.author ?? '').isEmpty)) {
        provider.value = ProviderServiceModel(
          author: '',
          authorName: '',
          title: categoryTitle.value,
          categoryId: categoryId.value,
          sectionId: Constant.sectionConstantModel?.id,
          publish: true,
          price: '',
          disPrice: '0',
        );
      }
    }
    selectedAddress.value = Constant.selectedLocation;
    final asap = args?['asap'] == true;
    if ((isBroadcast.value || asap) && dateTimeController.value.text.isEmpty) {
      setDateTime(DateTime.now());
    }
    fetchCoupons();
    calculatePrice();
  }

  void fetchCoupons() {
    if (provider.value?.author != null && provider.value!.author!.isNotEmpty) {
      FireStoreUtils.getProviderCoupon(provider.value!.author!).then((activeCoupons) => couponList.assignAll(activeCoupons));
      FireStoreUtils.getProviderCouponAfterExpire(provider.value!.author!).then((expiredCoupons) => couponList.addAll(expiredCoupons));
    }
  }

  void incrementQuantity() {
    quantity.value++;
    calculatePrice();
  }

  void decrementQuantity() {
    if (quantity.value > 1) {
      quantity.value--;
      calculatePrice();
    }
  }

  void setDateTime(DateTime dateTime) {
    selectedDateTime.value = dateTime;
    dateTimeText.value = DateFormat('dd-MM-yyyy HH:mm').format(dateTime);
    dateTimeController.value.text = dateTimeText.value;
  }

  bool get isAsap {
    return selectedDateTime.value.difference(DateTime.now()).inMinutes.abs() <= 15;
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

  String getDate(String date) {
    try {
      DateTime dt = DateTime.parse(date);
      return "${dt.day}-${dt.month}-${dt.year}";
    } catch (e) {
      return date;
    }
  }

  void calculatePrice() {
    orderTaxAmount.value = 0.0;
    platformTaxAmount.value = 0.0;
    taxAmount.value = 0.0;
    subTotal.value = 0.0;
    totalAmount.value = 0.0;
    double basePrice =
        (provider.value?.disPrice == "" || provider.value?.disPrice == "0")
            ? double.tryParse(provider.value?.price.toString() ?? "0") ?? 0
            : double.tryParse(provider.value?.disPrice.toString() ?? "0") ?? 0;

    price.value = basePrice * quantity.value;

    // discount
    if (discountType.value == "Percentage" || discountType.value == "Percent") {
      discountAmount.value = price.value * (double.tryParse(discountLabel.value) ?? 0) / 100;
    } else {
      discountAmount.value = double.tryParse(discountLabel.value.isEmpty ? '0' : discountLabel.value) ?? 0;
    }

    subTotal.value = price.value - discountAmount.value;

    for (var taxElement in Constant.orderProductTaxList ?? []) {
      orderTaxAmount.value += Constant.calculateTax(amount: (subTotal.value).toString(), taxModel: taxElement);
    }

    if (double.parse(Constant.platformFeeModel?.fee ?? '0.0') > 0.0 && Constant.platformFeeModel?.enable == true) {
      for (var taxElement in Constant.platformTaxList ?? []) {
        platformTaxAmount.value += Constant.calculateTax(amount: Constant.platformFeeModel?.fee ?? '0.0', taxModel: taxElement);
      }
    }

    taxAmount.value = orderTaxAmount.value + platformTaxAmount.value;

    totalAmount.value = (subTotal.value) + double.parse(Constant.platformFeeModel?.fee ?? '0.0') + taxAmount.value;
  }

  Future<void> confirmBooking(BuildContext context) async {
    if (selectedAddress.value.getFullAddress().isEmpty) {
      ShowToastDialog.showToast("Please enter address".tr);
    } else if (dateTimeController.value.text.isEmpty) {
      ShowToastDialog.showToast("Please select time slot.".tr);
    } else if (isBroadcast.value) {
      await _placeBroadcastRequest();
    } else {
      UserModel? providerUser = await FireStoreUtils.getUserProfile(provider.value!.author!);
      if (!FireStoreUtils.isAccountActive(providerUser)) {
        ShowToastDialog.showToast('Este prestador está indisponível.'.tr);
        return;
      }

      if (!HourlyServiceBilling.isHourly(provider.value?.priceUnit)) {
        OnProviderOrderModel onDemandOrderModel = OnProviderOrderModel(
          authorID: FireStoreUtils.getCurrentUid(),
          author: Constant.userModel!,
          quantity: double.parse(quantity.value.toString()),
          sectionId: Constant.sectionConstantModel!.id,
          address: selectedAddress.value,

          provider: provider.value,
          status: Constant.orderPlaced,
          scheduleDateTime: Timestamp.fromDate(selectedDateTime.value),
          notes: descriptionController.value.text,
          discount: discountAmount.toString(),
          discountType: discountType.toString(),
          discountLabel: discountLabel.toString(),
          adminCommission:
              Constant.sectionConstantModel?.adminCommision?.isEnabled == false ? '0' : "${providerUser?.adminCommissionModel?.amount ?? Constant.sectionConstantModel?.adminCommision?.amount ?? 0}",
          adminCommissionType:
              Constant.sectionConstantModel?.adminCommision?.isEnabled == false
                  ? 'fixed'
                  : providerUser?.adminCommissionModel?.commissionType ?? Constant.sectionConstantModel?.adminCommision?.commissionType,
          otp: Constant.getReferralCode(),
          couponCode: offerCode.toString(),
          taxModel: Constant.orderProductTaxList,
          platformFee: Constant.platformFeeModel?.fee ?? '0.0',
          platformTax: Constant.platformTaxList,
          dispatchMode: Constant.dispatchDirect,
        );
        print('totalAmount ::::::: ${double.tryParse(Constant.amountShow(amount: totalAmount.value.toString())) ?? 0.0}');
        print('totalAmount value ::::::: ${totalAmount.value}');

        Get.to(() => OnDemandPaymentScreen(), arguments: {'onDemandOrderModel': Rxn<OnProviderOrderModel>(onDemandOrderModel), 'totalAmount': totalAmount.value, 'isExtra': false});
      } else {
        ShowToastDialog.showLoader("Please wait...".tr);
        OnProviderOrderModel onDemandOrder = OnProviderOrderModel(
          otp: Constant.getReferralCode(),
          authorID: FireStoreUtils.getCurrentUid(),
          author: Constant.userModel!,
          sectionId: Constant.sectionConstantModel!.id,
          address: selectedAddress.value,
          status: Constant.orderPlaced,
          createdAt: Timestamp.now(),
          quantity: quantity.value < 1 ? 1.0 : quantity.value.toDouble(),
          provider: provider.value,
          extraPaymentStatus: true,
          scheduleDateTime: Timestamp.fromDate(selectedDateTime.value),
          notes: descriptionController.value.text,
          adminCommission:
              Constant.sectionConstantModel?.adminCommision?.isEnabled == false ? '0' : "${providerUser?.adminCommissionModel?.amount ?? Constant.sectionConstantModel?.adminCommision?.amount ?? 0}",
          adminCommissionType:
              Constant.sectionConstantModel?.adminCommision?.isEnabled == false
                  ? 'fixed'
                  : providerUser?.adminCommissionModel?.commissionType ?? Constant.sectionConstantModel?.adminCommision?.commissionType,
          paymentStatus: false,
          taxModel: Constant.orderProductTaxList,
          platformFee: Constant.platformFeeModel?.fee ?? '0.0',
          platformTax: Constant.platformTaxList,
          dispatchMode: Constant.dispatchDirect,
        );

        await FireStoreUtils.onDemandOrderPlace(onDemandOrder, 0.0);
        await FireStoreUtils.sendOrderOnDemandServiceEmail(orderModel: onDemandOrder);

        if (providerUser != null) {
          Map<String, dynamic> payLoad = {"type": 'provider_order', "orderId": onDemandOrder.id};
          await SendNotification.sendFcmMessage(Constant.bookingPlaced, providerUser.fcmToken.toString(), payLoad);
        }

        ShowToastDialog.closeLoader();
        Get.offAll(const OnDemandDashboardScreen());
        OnDemandDashboardController controller = Get.put(OnDemandDashboardController());
        controller.selectedIndex.value = 2;
        ShowToastDialog.showToast("OnDemand Service successfully booked".tr);
      }
    }
  }

  Future<void> _placeBroadcastRequest() async {
    if (Constant.userModel == null) {
      ShowToastDialog.showToast("Please enter address".tr);
      return;
    }
    ShowToastDialog.showLoader("Please wait...".tr);
    final template = provider.value ??
        ProviderServiceModel(
          author: '',
          authorName: '',
          title: categoryTitle.value,
          categoryId: categoryId.value,
          sectionId: Constant.sectionConstantModel?.id,
          publish: true,
        );
    template.author = '';
    template.authorName = '';
    if ((template.title ?? '').isEmpty) template.title = categoryTitle.value;
    if ((template.categoryId ?? '').isEmpty) template.categoryId = categoryId.value;

    final order = OnProviderOrderModel(
      otp: Constant.getReferralCode(),
      authorID: FireStoreUtils.getCurrentUid(),
      author: Constant.userModel!,
      sectionId: Constant.sectionConstantModel!.id,
      address: selectedAddress.value,
      status: Constant.orderPlaced,
      createdAt: Timestamp.now(),
      quantity: 1,
      provider: template,
      extraPaymentStatus: true,
      paymentStatus: false,
      scheduleDateTime: Timestamp.fromDate(selectedDateTime.value),
      notes: descriptionController.value.text,
      taxModel: Constant.orderProductTaxList,
      platformFee: Constant.platformFeeModel?.fee ?? '0.0',
      platformTax: Constant.platformTaxList,
      dispatchMode: Constant.dispatchBroadcast,
      requestedCategoryId: categoryId.value.isNotEmpty ? categoryId.value : template.categoryId,
      radiusKm: FireStoreUtils.nearbyRadiusKm(),
      rejectedBy: const [],
      offeredTo: const [],
      dispatchExpiresAt: Timestamp.fromDate(DateTime.now().add(DispatchOffer.jobTtl)),
    );

    await FireStoreUtils.onDemandOrderPlace(order, 0.0);
    try {
      final next = await BroadcastDispatch.advance(order.id);
      if (next != null && (next.fcmToken ?? '').isNotEmpty) {
        await SendNotification.sendOneNotification(
          token: next.fcmToken!,
          title: 'Novo pedido próximo',
          body: '${order.provider.title ?? 'Serviço'} · ${order.address?.getFullAddress() ?? ''}'.trim(),
          payload: {'type': DispatchOffer.fcmTypeOffer, 'orderId': order.id},
        );
      }
    } catch (_) {}
    ShowToastDialog.closeLoader();
    Get.to(() => const OnDemandBroadcastWaitingScreen(), arguments: order);
  }
}
