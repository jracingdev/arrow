import 'package:arrow_shared/dispatch_accept.dart';
import 'package:arrow_shared/geo_distance.dart';

/// Candidato a ping sequencial (mais próximo primeiro).
class DispatchCandidate {
  const DispatchCandidate({
    required this.uid,
    this.lat,
    this.lng,
    this.online = false,
    this.verified = false,
    this.fcmToken,
  });

  final String uid;
  final double? lat;
  final double? lng;
  final bool online;
  final bool verified;
  final String? fcmToken;
}

/// Janela Uber-style: ping de 25s no prestador atual e TTL global de 10 min.
class DispatchOffer {
  DispatchOffer._();

  static const offerWindow = Duration(seconds: 25);
  static const jobTtl = Duration(minutes: 10);
  static const offerWindowSeconds = 25;
  static const jobTtlMinutes = 10;
  static const cancelReasonNoProvider = 'no_provider';
  static const fcmTypeOffer = 'provider_dispatch_offer';
  static const fcmTypeOrder = 'provider_order';

  static DateTime jobExpiresAt({DateTime? createdAt, DateTime? dispatchExpiresAt, DateTime? now}) {
    if (dispatchExpiresAt != null) return dispatchExpiresAt;
    final start = createdAt ?? now ?? DateTime.now();
    return start.add(jobTtl);
  }

  static DateTime offerDeadline(DateTime now) => now.add(offerWindow);

  static int remainingJobSeconds({DateTime? createdAt, DateTime? dispatchExpiresAt, required DateTime now}) {
    final end = jobExpiresAt(createdAt: createdAt, dispatchExpiresAt: dispatchExpiresAt, now: now);
    final left = end.difference(now).inSeconds;
    return left < 0 ? 0 : left;
  }

  static bool isJobExpired({DateTime? createdAt, DateTime? dispatchExpiresAt, required DateTime now}) {
    return !now.isBefore(jobExpiresAt(createdAt: createdAt, dispatchExpiresAt: dispatchExpiresAt, now: now));
  }

  static bool isOfferExpired({DateTime? offerExpiresAt, required DateTime now}) {
    if (offerExpiresAt == null) return true;
    return !now.isBefore(offerExpiresAt);
  }

  static bool isJobOpen({required String status, required String? assignedAuthor}) {
    return status == DispatchAcceptGuard.orderPlaced && !DispatchAcceptGuard.hasAssignedProvider(assignedAuthor);
  }

  static bool shouldRing({required String uid, required String? offeredTo}) {
    final me = uid.trim();
    return me.isNotEmpty && me == (offeredTo ?? '').trim();
  }

  static bool shouldAdvanceOffer({
    required String status,
    required String? assignedAuthor,
    required String? offeredTo,
    DateTime? offerExpiresAt,
    DateTime? createdAt,
    DateTime? dispatchExpiresAt,
    required DateTime now,
  }) {
    if (!isJobOpen(status: status, assignedAuthor: assignedAuthor)) return false;
    if (isJobExpired(createdAt: createdAt, dispatchExpiresAt: dispatchExpiresAt, now: now)) return false;
    if ((offeredTo ?? '').trim().isEmpty) return true;
    return isOfferExpired(offerExpiresAt: offerExpiresAt, now: now);
  }

  static Set<String> excludedUids({
    required Iterable<String> rejectedBy,
    required Iterable<String> offeredTo,
  }) {
    return {
      ...rejectedBy.map((e) => e.trim()),
      ...offeredTo.map((e) => e.trim()),
    }..removeWhere((e) => e.isEmpty);
  }

  /// Próximo prestador online mais próximo, excluindo quem já recusou ou já foi pingado.
  static DispatchCandidate? pickNextClosest({
    required List<DispatchCandidate> candidates,
    required List<String> rejectedBy,
    required List<String> offeredTo,
    required double? originLat,
    required double? originLng,
    double? radiusKm,
    bool requireVerified = false,
    bool requireOnline = true,
  }) {
    final excluded = excludedUids(rejectedBy: rejectedBy, offeredTo: offeredTo);
    final eligible = candidates.where((c) {
      if (c.uid.trim().isEmpty || excluded.contains(c.uid.trim())) return false;
      if (requireOnline && !c.online) return false;
      if (requireVerified && !c.verified) return false;
      if (radiusKm != null && radiusKm > 0) {
        final km = GeoDistance.km(
          fromLat: originLat,
          fromLng: originLng,
          toLat: c.lat,
          toLng: c.lng,
        );
        if (km != null && km > radiusKm) return false;
      }
      return true;
    }).toList();

    eligible.sort((a, b) {
      final da = GeoDistance.km(fromLat: originLat, fromLng: originLng, toLat: a.lat, toLng: a.lng);
      final db = GeoDistance.km(fromLat: originLat, fromLng: originLng, toLat: b.lat, toLng: b.lng);
      return GeoDistance.compareKm(da, db);
    });
    return eligible.isEmpty ? null : eligible.first;
  }
}
