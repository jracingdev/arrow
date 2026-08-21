import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('customer Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.customer,
      '1:661081769489:android:d8da3fce389fcabca4d3b0',
    );
  });
}
