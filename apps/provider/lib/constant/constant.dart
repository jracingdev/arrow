import 'package:arrow_shared/arrow_currency.dart';
import 'package:arrow_shared/arrow_payment_label.dart';
import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/brazil_phone.dart';
import 'package:intl/intl.dart';
import 'package:provider/models/user_model.dart';
import 'package:uuid/uuid.dart';

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
  static const List<String> tabAccepted = [orderAccepted];
  static const List<String> tabOngoing = [orderAssigned, orderOngoing, inTransit];
  static const List<String> tabActive = [orderAccepted, orderAssigned, orderOngoing, inTransit];
  static const List<String> tabUpcoming = [orderAccepted, orderAssigned, orderOngoing, inTransit];
  static const List<String> tabCompleted = [orderCompleted];
  static const List<String> tabCancelled = [orderRejected, orderCancelled, driverRejected];

  static const String invoiceTypeNfse = 'nfs-e';
  static const int invoiceMaxBytes = 10 * 1024 * 1024;
  static const List<String> invoiceAllowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

  static const String bookingCreditNote = 'On-demand booking credited';
  static const String extraCreditNote = 'Extra Charge Amount Credited';
  static const String dispatchBroadcast = 'broadcast';
  static const String dispatchDirect = 'direct';
  static const Duration broadcastLookback = Duration(hours: 6);
  static const double defaultBroadcastRadiusKm = 25;

  static bool canUploadInvoice(String status) {
    return status == orderOngoing || status == inTransit || status == orderCompleted;
  }

  static String defaultCountryCode = BrazilPhone.dialCode;
  static String defaultCountryISOCode = BrazilPhone.isoCode;

  static UserModel? userModel;

  static String getUuid() => const Uuid().v4();

  static bool isCod(String? method) => ArrowPaymentLabel.isCod(method);

  static String paymentLabel({required String method, bool? paid}) {
    return ArrowPaymentLabel.withStatus(method: method, paid: paid);
  }

  static String amountShow({required String? amount}) {
    final value = double.tryParse((amount == null || amount.isEmpty) ? '0' : amount) ?? 0.0;
    final number = NumberFormat.currency(locale: 'pt_BR', symbol: '', decimalDigits: ArrowCurrency.decimals).format(value).trim();
    return '${ArrowCurrency.symbol} $number';
  }

  static bool canReportOrder(String status) {
    return status == orderAssigned ||
        status == orderOngoing ||
        status == orderCompleted ||
        status == orderCancelled ||
        status == orderAccepted ||
        status == inTransit;
  }

  static bool canSosOrder(String status) {
    return status == orderOngoing || status == inTransit || status == orderAssigned;
  }

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
