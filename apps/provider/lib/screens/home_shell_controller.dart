import 'package:get/get.dart';

class HomeShellController extends GetxController {
  final index = 0.obs;
  final ordersTab = 0.obs;

  void goTo(int value) => index.value = value;

  void openOrders({int tab = 0}) {
    ordersTab.value = tab;
    index.value = 1;
  }
}
