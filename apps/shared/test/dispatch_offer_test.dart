import 'package:arrow_shared/dispatch_offer.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  const originLat = -23.5505;
  const originLng = -46.6333;

  DispatchCandidate c({
    required String uid,
    double lat = -23.551,
    double lng = -46.634,
    bool online = true,
    bool verified = true,
  }) {
    return DispatchCandidate(uid: uid, lat: lat, lng: lng, online: online, verified: verified);
  }

  test('escolhe o mais próximo e exclui rejectedBy e offeredTo', () {
    final near = c(uid: 'near', lat: -23.551, lng: -46.634);
    final mid = c(uid: 'mid', lat: -23.56, lng: -46.65);
    final far = c(uid: 'far', lat: -22.90, lng: -43.17);

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [far, mid, near],
        rejectedBy: const [],
        offeredTo: const [],
        originLat: originLat,
        originLng: originLng,
      )?.uid,
      'near',
    );

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [far, mid, near],
        rejectedBy: const ['near'],
        offeredTo: const [],
        originLat: originLat,
        originLng: originLng,
      )?.uid,
      'mid',
    );

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [far, mid, near],
        rejectedBy: const ['near'],
        offeredTo: const ['mid'],
        originLat: originLat,
        originLng: originLng,
      )?.uid,
      'far',
    );

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [far, mid, near],
        rejectedBy: const ['near', 'far'],
        offeredTo: const ['mid'],
        originLat: originLat,
        originLng: originLng,
      ),
      isNull,
    );
  });

  test('ignora offline e respeita raio / verificado', () {
    final offlineNear = c(uid: 'offline', online: false);
    final unverified = c(uid: 'unverified', lat: -23.552, lng: -46.635, verified: false);
    final inside = c(uid: 'inside', lat: -23.555, lng: -46.640);
    final outside = c(uid: 'outside', lat: -22.90, lng: -43.17);

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [offlineNear, inside],
        rejectedBy: const [],
        offeredTo: const [],
        originLat: originLat,
        originLng: originLng,
      )?.uid,
      'inside',
    );

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [unverified, inside],
        rejectedBy: const [],
        offeredTo: const [],
        originLat: originLat,
        originLng: originLng,
        requireVerified: true,
      )?.uid,
      'inside',
    );

    expect(
      DispatchOffer.pickNextClosest(
        candidates: [inside, outside],
        rejectedBy: const [],
        offeredTo: const [],
        originLat: originLat,
        originLng: originLng,
        radiusKm: 10,
      )?.uid,
      'inside',
    );
  });

  test('janela de 25s e fechamento em 10 min', () {
    expect(DispatchOffer.offerWindowSeconds, 25);
    expect(DispatchOffer.jobTtlMinutes, 10);
    final created = DateTime(2026, 8, 22, 10, 0);
    final now = created.add(const Duration(minutes: 10));
    expect(DispatchOffer.isJobExpired(createdAt: created, now: now), isTrue);
    expect(
      DispatchOffer.remainingJobSeconds(createdAt: created, now: created.add(const Duration(minutes: 3, seconds: 20))),
      6 * 60 + 40,
    );
    expect(
      DispatchOffer.isOfferExpired(offerExpiresAt: created.add(const Duration(seconds: 25)), now: created.add(const Duration(seconds: 25))),
      isTrue,
    );
    expect(
      DispatchOffer.shouldAdvanceOffer(
        status: 'Order Placed',
        assignedAuthor: '',
        offeredTo: 'p1',
        offerExpiresAt: created.add(const Duration(seconds: 25)),
        createdAt: created,
        now: created.add(const Duration(seconds: 26)),
      ),
      isTrue,
    );
    expect(DispatchOffer.shouldRing(uid: 'p1', offeredTo: 'p1'), isTrue);
    expect(DispatchOffer.shouldRing(uid: 'p2', offeredTo: 'p1'), isFalse);
  });
}
