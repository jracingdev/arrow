import 'package:arrow_shared/dispatch_offer.dart';
import 'package:arrow_shared/geo_distance.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:customer/constant/collection_name.dart';
import 'package:customer/constant/constant.dart';
import 'package:customer/models/onprovider_order_model.dart';
import 'package:customer/service/fire_store_utils.dart';

class BroadcastDispatch {
  BroadcastDispatch._();

  static FirebaseFirestore get _db => FireStoreUtils.fireStore;

  static Future<bool> cancelUnassigned({
    required String orderId,
    String reason = '',
    String cancelReason = '',
  }) async {
    if (orderId.isEmpty) return false;
    final ref = _db.collection(CollectionName.providerOrders).doc(orderId);
    try {
      await _db.runTransaction((tx) async {
        final snap = await tx.get(ref);
        if (!snap.exists || snap.data() == null) {
          throw Exception('Pedido não encontrado');
        }
        final data = snap.data()!;
        final author = (data['provider'] is Map ? (data['provider'] as Map)['author'] : '')?.toString() ?? '';
        if (author.trim().isNotEmpty) {
          throw StateError('accepted');
        }
        tx.update(ref, {
          'status': Constant.orderCancelled,
          'reason': reason.isEmpty ? 'Cliente cancelou a busca' : reason,
          if (cancelReason.isNotEmpty) 'cancelReason': cancelReason,
        });
      });
      return true;
    } on StateError {
      return false;
    }
  }

  static Future<List<DispatchCandidate>> listCandidates({
    required String categoryId,
    required List<String> rejectedBy,
    required List<String> offeredTo,
  }) async {
    Query<Map<String, dynamic>> query = _db.collection(CollectionName.providersServices).where('publish', isEqualTo: true);
    if (categoryId.isNotEmpty) {
      query = query.where('categoryId', isEqualTo: categoryId);
    }
    final snap = await query.get();
    final byAuthor = <String, Map<String, dynamic>>{};
    for (final doc in snap.docs) {
      final data = doc.data();
      final author = data['author']?.toString() ?? '';
      if (author.isEmpty) continue;
      byAuthor.putIfAbsent(author, () => data);
    }
    final exclude = DispatchOffer.excludedUids(rejectedBy: rejectedBy, offeredTo: offeredTo);
    final uids = byAuthor.keys.where((id) => !exclude.contains(id)).toList();
    final candidates = <DispatchCandidate>[];
    for (var i = 0; i < uids.length; i += 10) {
      final chunk = uids.skip(i).take(10).toList();
      final users = await Future.wait(chunk.map((id) => _db.collection(CollectionName.users).doc(id).get()));
      for (var j = 0; j < users.length; j++) {
        final userSnap = users[j];
        if (!userSnap.exists || userSnap.data() == null) continue;
        final user = userSnap.data()!;
        final service = byAuthor[chunk[j]] ?? const <String, dynamic>{};
        final loc = user['location'];
        final coords = GeoDistance.resolve(
          serviceLat: double.tryParse('${service['latitude'] ?? ''}'),
          serviceLng: double.tryParse('${service['longitude'] ?? ''}'),
          userLat: loc is Map ? double.tryParse('${loc['latitude'] ?? ''}') : null,
          userLng: loc is Map ? double.tryParse('${loc['longitude'] ?? ''}') : null,
          vendorLat: double.tryParse('${user['latitude'] ?? ''}'),
          vendorLng: double.tryParse('${user['longitude'] ?? ''}'),
        );
        candidates.add(DispatchCandidate(
          uid: chunk[j],
          lat: coords?.lat,
          lng: coords?.lng,
          online: user['online'] == true,
          verified: user['isDocumentVerify'] == true,
          fcmToken: user['fcmToken']?.toString(),
        ));
      }
    }
    return candidates;
  }

  static Future<DispatchCandidate?> advance(String orderId) async {
    if (orderId.isEmpty) return null;
    final ref = _db.collection(CollectionName.providerOrders).doc(orderId);
    final snap = await ref.get();
    if (!snap.exists || snap.data() == null) return null;
    final order = OnProviderOrderModel.fromJson(snap.data()!);
    final now = DateTime.now();
    if (!DispatchOffer.isJobOpen(status: order.status, assignedAuthor: order.provider.author)) return null;
    if (DispatchOffer.isJobExpired(
      createdAt: order.createdAt.toDate(),
      dispatchExpiresAt: order.dispatchExpiresAt?.toDate(),
      now: now,
    )) {
      await cancelUnassigned(
        orderId: orderId,
        reason: 'Nenhum prestador disponível',
        cancelReason: DispatchOffer.cancelReasonNoProvider,
      );
      return null;
    }
    final stale = (order.dispatchOfferedTo ?? '').trim();
    var rejected = List<String>.from(order.rejectedBy);
    var offered = List<String>.from(order.offeredTo);
    if (stale.isNotEmpty && DispatchOffer.isOfferExpired(offerExpiresAt: order.dispatchOfferExpiresAt?.toDate(), now: now)) {
      if (!rejected.contains(stale)) rejected.add(stale);
      if (!offered.contains(stale)) offered.add(stale);
    }
    final categoryId = (order.requestedCategoryId ?? '').isNotEmpty ? order.requestedCategoryId! : (order.provider.categoryId ?? '');
    final candidates = await listCandidates(categoryId: categoryId, rejectedBy: rejected, offeredTo: offered);
    final next = DispatchOffer.pickNextClosest(
      candidates: candidates,
      rejectedBy: rejected,
      offeredTo: offered,
      originLat: order.customerLat(),
      originLng: order.customerLng(),
      radiusKm: order.radiusKm ?? FireStoreUtils.nearbyRadiusKm(),
    );
    await _db.runTransaction((tx) async {
      final fresh = await tx.get(ref);
      if (!fresh.exists || fresh.data() == null) return;
      final current = OnProviderOrderModel.fromJson(fresh.data()!);
      final tick = DateTime.now();
      if (!DispatchOffer.shouldAdvanceOffer(
        status: current.status,
        assignedAuthor: current.provider.author,
        offeredTo: current.dispatchOfferedTo,
        offerExpiresAt: current.dispatchOfferExpiresAt?.toDate(),
        createdAt: current.createdAt.toDate(),
        dispatchExpiresAt: current.dispatchExpiresAt?.toDate(),
        now: tick,
      )) {
        return;
      }
      final timedOut = (current.dispatchOfferedTo ?? '').trim();
      final patch = <String, dynamic>{};
      if (timedOut.isNotEmpty) {
        patch['rejectedBy'] = FieldValue.arrayUnion([timedOut]);
        patch['offeredTo'] = FieldValue.arrayUnion([timedOut]);
      }
      if (next == null) {
        patch['dispatchOfferedTo'] = '';
        patch['dispatchOfferExpiresAt'] = FieldValue.delete();
        tx.update(ref, patch);
        return;
      }
      patch['dispatchOfferedTo'] = next.uid;
      patch['dispatchOfferExpiresAt'] = Timestamp.fromDate(DispatchOffer.offerDeadline(tick));
      patch['offeredTo'] = FieldValue.arrayUnion([
        if (timedOut.isNotEmpty) timedOut,
        next.uid,
      ]);
      tx.update(ref, patch);
    });
    return next;
  }
}
