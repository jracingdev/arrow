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
  static const other = 'other';

  static const all = [abuse, harassment, noShow, badService, paymentFraud, other];

  static String labelPt(String category) {
    switch (category) {
      case abuse:
        return 'Abuso';
      case harassment:
        return 'Assédio';
      case noShow:
        return 'Não compareceu';
      case badService:
        return 'Péssimo atendimento';
      case paymentFraud:
        return 'Levou o dinheiro e não fez';
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
