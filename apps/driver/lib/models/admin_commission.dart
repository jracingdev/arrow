class AdminCommission {
  String? amount;
  bool? isEnabled;
  String? commissionType;

  AdminCommission({this.amount, this.isEnabled, this.commissionType});

  AdminCommission.fromJson(Map<String, dynamic> json) {
    amount = json['fix_commission']?.toString();
    isEnabled = json['isEnabled'] == true;
    commissionType = json['commissionType']?.toString();
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['fix_commission'] = amount;
    data['isEnabled'] = isEnabled;
    data['commissionType'] = commissionType;
    return data;
  }
}
