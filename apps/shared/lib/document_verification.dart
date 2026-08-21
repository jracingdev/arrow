/// Public KYC flag. Customers never see the files — only this badge.
class DocumentVerification {
  DocumentVerification._();

  /// True when the platform admin approved the person's documents.
  /// [isAutoVerify] does not count: that flag only skips the applicant gate.
  static bool isVerifiedForPublic({bool? isDocumentVerify, bool? isAutoVerify}) {
    return isDocumentVerify == true;
  }
}
