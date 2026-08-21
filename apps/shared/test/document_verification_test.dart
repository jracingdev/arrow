import 'package:arrow_shared/document_verification.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('isVerifiedForPublic is true only after admin approval', () {
    expect(DocumentVerification.isVerifiedForPublic(), isFalse);
    expect(DocumentVerification.isVerifiedForPublic(isDocumentVerify: false), isFalse);
    expect(DocumentVerification.isVerifiedForPublic(isAutoVerify: true), isFalse);
    expect(DocumentVerification.isVerifiedForPublic(isDocumentVerify: true), isTrue);
    expect(
      DocumentVerification.isVerifiedForPublic(isDocumentVerify: true, isAutoVerify: true),
      isTrue,
    );
  });
}
