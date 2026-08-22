/// Recidivism helpers for on-demand safety reports (`complaints` collection).
///
/// Bans still go through admin (`users.active` / `users.isActive`). This only
/// increments strikes and flags `banRecommended` at the threshold.
class ReportStrikes {
  ReportStrikes._();

  static const recommendBanAt = 3;

  static int increment(int current) => (current < 0 ? 0 : current) + 1;

  static bool shouldRecommendBan(int strikes, {int threshold = recommendBanAt}) {
    return strikes >= threshold;
  }
}

class ReportCategories {
  static const abuse = 'abuse';
  static const harassment = 'harassment';
  static const noShow = 'no_show';
  static const badService = 'bad_service';
  static const paymentFraud = 'payment_fraud';
  static const unsafeSituation = 'unsafe_situation';
  static const paymentDispute = 'payment_dispute';
  static const other = 'other';

  /// Provider reporting the customer. Omits customer-only service/payment-fraud chips.
  static const forProvider = [abuse, harassment, noShow, unsafeSituation, paymentDispute, other];

  /// Customer reporting the provider.
  static const forCustomer = [abuse, harassment, noShow, badService, paymentFraud, other];

  static const all = [abuse, harassment, noShow, badService, paymentFraud, unsafeSituation, paymentDispute, other];

  static List<String> forRole(String role) => role == 'provider' ? forProvider : forCustomer;

  static String labelPt(String category, {String role = 'customer'}) {
    switch (category) {
      case abuse:
        return 'Abuso';
      case harassment:
        return 'Assédio';
      case noShow:
        return role == 'provider' ? 'Cliente não estava no local' : 'Prestador não compareceu';
      case badService:
        return 'Péssimo atendimento';
      case paymentFraud:
        return 'Levou o dinheiro e não fez o serviço';
      case unsafeSituation:
        return 'Situação de risco';
      case paymentDispute:
        return 'Cliente recusou pagar';
      default:
        return 'Outro';
    }
  }
}

/// Maps to the existing cab `complaints.status` values so admin can reuse them.
class ComplaintStatuses {
  static const pending = 'Initiated';
  static const reviewed = 'Under Investigation';
  static const actioned = 'Resolved';
  static const dismissed = 'Dismissed';
}

class UserActiveFlag {
  static bool fromFields({bool? active, bool? isActive}) {
    return active == true || isActive == true;
  }
}
