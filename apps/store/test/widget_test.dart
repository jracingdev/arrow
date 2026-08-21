import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('store Firebase Android app id is j-arrow', () {
    expect(
      ArrowFirebaseAndroidAppIds.store,
      '1:661081769489:android:c625e7c47a334c31a4d3b0',
    );
  });
}
