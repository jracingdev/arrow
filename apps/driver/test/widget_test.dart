import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('driver Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.driver,
      '1:661081769489:android:246c57cb98fff558a4d3b0',
    );
  });
}
