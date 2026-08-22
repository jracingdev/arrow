import 'package:arrow_shared/published_service_visibility.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('serviço publicado sem plano continua visível', () {
    expect(
      PublishedServiceVisibility.passesListing(
        subscriptionModelApplied: false,
        hasPlan: false,
        expired: false,
      ),
      isTrue,
    );
    expect(
      PublishedServiceVisibility.passesListing(
        subscriptionModelApplied: true,
        hasPlan: false,
        expired: false,
      ),
      isTrue,
    );
    expect(
      PublishedServiceVisibility.passesListing(
        subscriptionModelApplied: true,
        hasPlan: true,
        expired: true,
      ),
      isFalse,
    );
  });

  test('itemLimit ausente não zera a vitrine', () {
    expect(PublishedServiceVisibility.includePublishedAt(index: 0, itemLimit: null), isTrue);
    expect(PublishedServiceVisibility.includePublishedAt(index: 3, itemLimit: '2'), isFalse);
    expect(PublishedServiceVisibility.includePublishedAt(index: 3, itemLimit: '-1'), isTrue);
  });
}
