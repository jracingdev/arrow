import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:provider/models/user_model.dart';

class Constant {
  static const String userRoleProvider = 'provider';
  static const String androidPackage = ArrowAndroidPackages.provider;

  static const String orderPlaced = 'Order Placed';
  static const String orderAccepted = 'Order Accepted';
  static const String orderAssigned = 'Order Assigned';
  static const String orderOngoing = 'Order Ongoing';
  static const String orderCompleted = 'Order Completed';
  static const String orderRejected = 'Order Rejected';
  static const String orderCancelled = 'Order Cancelled';
  static const String inTransit = 'In Transit';
  static const String driverRejected = 'Driver Rejected';

  static const List<String> tabPlaced = [orderPlaced];
  static const List<String> tabActive = [orderAccepted, orderAssigned, orderOngoing, inTransit];
  static const List<String> tabCompleted = [orderCompleted];
  static const List<String> tabCancelled = [orderRejected, orderCancelled, driverRejected];

  static String defaultCountryCode = BrazilPhone.dialCode;
  static String defaultCountryISOCode = BrazilPhone.isoCode;

  static UserModel? userModel;

  static String statusLabel(String status) {
    switch (status) {
      case orderPlaced:
        return 'Pedido realizado';
      case orderAccepted:
        return 'Pedido aceito';
      case orderAssigned:
        return 'Pedido atribuído';
      case orderOngoing:
        return 'Em andamento';
      case orderCompleted:
        return 'Pedido concluído';
      case orderRejected:
        return 'Pedido recusado';
      case orderCancelled:
        return 'Pedido cancelado';
      case inTransit:
        return 'Em trânsito';
      case driverRejected:
        return 'Recusado pelo profissional';
      default:
        return status;
    }
  }
}
