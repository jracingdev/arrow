import 'package:arrow_shared/published_service_visibility.dart';
import 'package:customer/controllers/on_demand_home_controller.dart';
import 'package:get/get.dart';
import '../models/provider_serivce_model.dart';
import '../service/fire_store_utils.dart';

class ViewCategoryServiceController extends GetxController {
  RxBool isLoading = true.obs;
  RxBool verifiedOnly = false.obs;
  RxList<ProviderServiceModel> providerList = <ProviderServiceModel>[].obs;

  RxString categoryId = "".obs, categoryTitle = "".obs;
  Rx<OnDemandHomeController> onDemandHomeController = Get.find<OnDemandHomeController>().obs;

  List<ProviderServiceModel> get visibleProviders {
    if (!verifiedOnly.value) return providerList;
    return providerList.where((p) => p.authorDocumentVerify == true).toList();
  }

  @override
  void onInit() {
    super.onInit();

    final args = Get.arguments as Map<String, dynamic>;
    categoryId.value = args['categoryId'] ?? "";
    categoryTitle.value = args['categoryTitle'] ?? "";

    getData();
  }

  Future<void> getData() async {
    providerList.clear();
    isLoading.value = true;

    final providerServiceList = await FireStoreUtils.getProviderFuture(categoryId: categoryId.value);
    providerList.addAll(
      PublishedServiceVisibility.takeByAuthorItemLimit(
        services: providerServiceList,
        authorOf: (service) => service.author,
        itemLimitOf: (service) => service.subscriptionPlan?.itemLimit,
        compareCreated: (a, b) => (a.createdAt?.millisecondsSinceEpoch ?? 0).compareTo(b.createdAt?.millisecondsSinceEpoch ?? 0),
      ),
    );

    await FireStoreUtils.hideInactiveProviderAuthors(providerList);
    FireStoreUtils.sortProviderListing(providerList);
    providerList.refresh();
    isLoading.value = false;
  }
}
