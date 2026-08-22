import 'package:arrow_shared/published_service_visibility.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/controllers/on_demand_home_controller.dart';
import 'package:get/get.dart';
import 'package:flutter/material.dart';
import '../models/favorite_ondemand_service_model.dart';
import '../models/provider_serivce_model.dart';
import '../service/fire_store_utils.dart';

class ViewAllPopularServiceController extends GetxController {
  RxList<ProviderServiceModel> providerList = <ProviderServiceModel>[].obs;
  RxList<ProviderServiceModel> allProviderList = <ProviderServiceModel>[].obs;
  RxBool isLoading = true.obs;
  Rx<OnDemandHomeController> onDemandHomeController = Get.find<OnDemandHomeController>().obs;

  final OnDemandHomeController onDemandController = Get.find<OnDemandHomeController>();

  Rx<TextEditingController> searchTextFiledController = TextEditingController().obs;

  RxList<FavouriteOndemandServiceModel> lstFav = <FavouriteOndemandServiceModel>[].obs;

  @override
  void onInit() {
    super.onInit();
    getData();
  }

  Future<void> getData() async {
    isLoading.value = true;

    await FireStoreUtils.getProviderFuture()
        .then((providerServiceList) {
          final filteredProviders = PublishedServiceVisibility.takeByAuthorItemLimit(
            services: providerServiceList,
            authorOf: (service) => service.author,
            itemLimitOf: (service) => service.subscriptionPlan?.itemLimit,
            compareCreated: (a, b) => (a.createdAt?.millisecondsSinceEpoch ?? 0).compareTo(b.createdAt?.millisecondsSinceEpoch ?? 0),
          );

          allProviderList.value = filteredProviders;
          providerList.value = filteredProviders;
          isLoading.value = false;
        })
        .catchError((e) {
          print("Provider error: $e");
          isLoading.value = false;
        });

    if (Constant.userModel != null) {
      await FireStoreUtils.getFavouritesServiceList(FireStoreUtils.getCurrentUid()).then((value) {
        lstFav.value = value;
      });
    }
    isLoading.value = false;
  }

  void getFilterData(String value) {
    if (value.isNotEmpty) {
      providerList.value = allProviderList.where((e) => e.title!.toLowerCase().contains(value.toLowerCase()) || e.title!.startsWith(value)).toList();
    } else {
      providerList.assignAll(allProviderList);
    }
  }
}
