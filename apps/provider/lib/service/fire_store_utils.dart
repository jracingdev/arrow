import 'dart:io';

import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_storage/firebase_storage.dart';
import 'package:provider/constant/collection_name.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/document_model.dart';
import 'package:provider/models/order_invoice_model.dart';
import 'package:provider/models/provider_document_model.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/models/rating_model.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/models/wallet_transaction_model.dart';
import 'package:provider/models/worker_model.dart';
import 'package:uuid/uuid.dart';

class FireStoreUtils {
  static FirebaseFirestore get _db => FirebaseFirestore.instance;

  static String getCurrentUid() => FirebaseAuth.instance.currentUser?.uid ?? '';

  static Future<UserModel?> getUserProfile(String uid) async {
    final snap = await _db.collection(CollectionName.users).doc(uid).get();
    if (!snap.exists || snap.data() == null) return null;
    return UserModel.fromJson(snap.data()!);
  }

  static Future<bool> userExists(String uid) async {
    final snap = await _db.collection(CollectionName.users).doc(uid).get();
    return snap.exists;
  }

  static Future<void> updateUser(UserModel user) async {
    if (user.id == null || user.id!.isEmpty) return;
    await _db.collection(CollectionName.users).doc(user.id).set(user.toJson(), SetOptions(merge: true));
  }

  static Stream<List<ProviderOrderModel>> watchMyOrders(String uid) {
    return _db
        .collection(CollectionName.providerOrders)
        .where('provider.author', isEqualTo: uid)
        .snapshots()
        .map((snap) {
      final list = snap.docs.map((d) => ProviderOrderModel.fromJson(d.data())).toList();
      list.sort((a, b) {
        final at = a.createdAt?.millisecondsSinceEpoch ?? 0;
        final bt = b.createdAt?.millisecondsSinceEpoch ?? 0;
        return bt.compareTo(at);
      });
      return list;
    });
  }

  static Stream<ProviderOrderModel?> watchOrder(String orderId) {
    return _db.collection(CollectionName.providerOrders).doc(orderId).snapshots().map((snap) {
      if (!snap.exists || snap.data() == null) return null;
      return ProviderOrderModel.fromJson(snap.data()!);
    });
  }

  static Future<void> updateOrder(String orderId, Map<String, dynamic> data) {
    return _db.collection(CollectionName.providerOrders).doc(orderId).update(data);
  }

  static String invoiceStoragePath(String orderId, String fileId, String extension) {
    return 'provider_orders/$orderId/invoices/$fileId.$extension';
  }

  static String _invoiceExtension(String fileName) {
    final dot = fileName.lastIndexOf('.');
    if (dot < 0 || dot == fileName.length - 1) return '';
    return fileName.substring(dot + 1).toLowerCase();
  }

  static String _invoiceContentType(String extension) {
    switch (extension) {
      case 'pdf':
        return 'application/pdf';
      case 'png':
        return 'image/png';
      case 'webp':
        return 'image/webp';
      case 'jpg':
      case 'jpeg':
        return 'image/jpeg';
      default:
        return 'application/octet-stream';
    }
  }

  static Future<void> uploadOrderInvoice({
    required String orderId,
    required File file,
    required String fileName,
    required List<OrderInvoiceModel> current,
    int? replaceIndex,
  }) async {
    final length = await file.length();
    if (length > Constant.invoiceMaxBytes) {
      throw Exception('O arquivo deve ter no máximo 10 MB.');
    }
    final extension = _invoiceExtension(fileName);
    if (!Constant.invoiceAllowedExtensions.contains(extension)) {
      throw Exception('Envie um PDF ou imagem (JPG, PNG, WEBP).');
    }
    final fileId = const Uuid().v4();
    final path = invoiceStoragePath(orderId, fileId, extension);
    final ref = FirebaseStorage.instance.ref().child(path);
    await ref.putFile(file, SettableMetadata(contentType: _invoiceContentType(extension)));
    final url = await ref.getDownloadURL();
    final invoice = OrderInvoiceModel(
      type: Constant.invoiceTypeNfse,
      url: url,
      fileName: fileName,
      uploadedAt: Timestamp.now(),
      uploadedBy: getCurrentUid(),
    );
    final next = List<OrderInvoiceModel>.from(current);
    if (replaceIndex != null && replaceIndex >= 0 && replaceIndex < next.length) {
      final previous = next[replaceIndex];
      next[replaceIndex] = invoice;
      if (previous.url.isNotEmpty) {
        try {
          await FirebaseStorage.instance.refFromURL(previous.url).delete();
        } catch (_) {}
      }
    } else {
      next.add(invoice);
    }
    await updateOrder(orderId, {'invoices': next.map((e) => e.toJson()).toList()});
  }

  static Stream<List<ProviderServiceModel>> watchMyServices(String uid) {
    return _db.collection(CollectionName.providersServices).where('author', isEqualTo: uid).snapshots().map((snap) {
      return snap.docs.map((d) => ProviderServiceModel.fromJson(d.data())).toList();
    });
  }

  static Future<void> setServicePublish(String serviceId, bool publish) {
    return _db.collection(CollectionName.providersServices).doc(serviceId).update({'publish': publish});
  }

  static Stream<List<WorkerModel>> watchMyWorkers(String uid) {
    return _db.collection(CollectionName.providersWorkers).where('providerId', isEqualTo: uid).snapshots().map((snap) {
      return snap.docs.map((d) => WorkerModel.fromJson(d.data())).toList();
    });
  }

  static Future<void> setWorkerOnline(String workerId, bool online) {
    return _db.collection(CollectionName.providersWorkers).doc(workerId).update({'online': online});
  }

  static Future<WorkerModel?> getWorker(String workerId) async {
    if (workerId.isEmpty) return null;
    final snap = await _db.collection(CollectionName.providersWorkers).doc(workerId).get();
    if (!snap.exists || snap.data() == null) return null;
    return WorkerModel.fromJson(snap.data()!);
  }

  static Stream<UserModel?> watchUser(String uid) {
    if (uid.isEmpty) return Stream.value(null);
    return _db.collection(CollectionName.users).doc(uid).snapshots().map((snap) {
      if (!snap.exists || snap.data() == null) return null;
      return UserModel.fromJson(snap.data()!);
    });
  }

  static Stream<List<WalletTransactionModel>> watchWalletTransactions(String uid) {
    if (uid.isEmpty) return Stream.value(const []);
    return _db.collection(CollectionName.wallet).where('user_id', isEqualTo: uid).snapshots().map((snap) {
      final list = snap.docs.map((d) => WalletTransactionModel.fromJson(d.data())).toList();
      list.sort((a, b) {
        final at = a.date?.millisecondsSinceEpoch ?? 0;
        final bt = b.date?.millisecondsSinceEpoch ?? 0;
        return bt.compareTo(at);
      });
      return list;
    });
  }

  static Stream<List<RatingModel>> watchMyReviews(String uid) {
    if (uid.isEmpty) return Stream.value(const []);
    return _db.collection(CollectionName.itemsReview).where('VendorId', isEqualTo: uid).snapshots().map((snap) {
      final list = snap.docs.map((d) => RatingModel.fromJson(d.data())).toList();
      list.sort((a, b) {
        final at = a.createdAt?.millisecondsSinceEpoch ?? 0;
        final bt = b.createdAt?.millisecondsSinceEpoch ?? 0;
        return bt.compareTo(at);
      });
      return list;
    });
  }

  static Future<bool> setWalletTransaction(WalletTransactionModel model) async {
    if (model.id == null || model.id!.isEmpty) return false;
    try {
      await _db.collection(CollectionName.wallet).doc(model.id).set(model.toJson());
      return true;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> updateUserWallet({required String amount, required String userId}) async {
    final user = await getUserProfile(userId);
    if (user == null) return false;
    user.walletAmount = (user.walletAmount ?? 0) + (num.tryParse(amount) ?? 0);
    await updateUser(user);
    return true;
  }

  static Future<bool> _hasProviderBookingCredit(String providerId, String orderId) async {
    if (providerId.isEmpty || orderId.isEmpty) return false;
    final snap = await _db
        .collection(CollectionName.wallet)
        .where('user_id', isEqualTo: providerId)
        .where('order_id', isEqualTo: orderId)
        .get();
    return snap.docs.any((d) {
      final data = d.data();
      if (data['transactionUser'] != 'provider' || data['isTopUp'] != true) return false;
      final note = (data['note'] ?? '').toString().toLowerCase();
      return !note.contains('extra');
    });
  }

  /// Credits the provider on COD completion (same `wallet` collection as delivery).
  static Future<void> creditCodOnComplete(ProviderOrderModel order) async {
    if (!Constant.isCod(order.paymentMethod)) return;
    final providerId = order.provider.author.isNotEmpty ? order.provider.author : getCurrentUid();
    if (providerId.isEmpty) return;
    if (await _hasProviderBookingCredit(providerId, order.id)) return;

    final rate = HourlyServiceBilling.unitPrice(order.provider.price, order.provider.disPrice);
    final hourly = HourlyServiceBilling.isHourly(order.provider.priceUnit);
    final hours = hourly ? (order.quantity > 0 ? order.quantity : 1.0) : 1.0;
    var amount = HourlyServiceBilling.amount(rate, hours);
    final extra = double.tryParse(order.extraCharges) ?? 0;
    if (extra > 0) {
      final extraSnap = await _db
          .collection(CollectionName.wallet)
          .where('user_id', isEqualTo: providerId)
          .where('order_id', isEqualTo: order.id)
          .get();
      final extraAlready = extraSnap.docs.any((d) {
        final data = d.data();
        if (data['transactionUser'] != 'provider' || data['isTopUp'] != true) return false;
        return (data['note'] ?? '').toString().toLowerCase().contains('extra');
      });
      if (!extraAlready) amount += extra;
    }
    if (amount <= 0) return;

    final tx = WalletTransactionModel(
      id: Constant.getUuid(),
      serviceType: 'ondemand-service',
      amount: amount,
      date: Timestamp.now(),
      paymentMethod: 'wallet',
      transactionUser: 'provider',
      userId: providerId,
      isTopup: true,
      orderId: order.id,
      note: Constant.bookingCreditNote,
      paymentStatus: 'success',
    );
    final ok = await setWalletTransaction(tx);
    if (ok) {
      await updateUserWallet(amount: amount.toString(), userId: providerId);
    }
  }

  static Future<List<DocumentModel>> getProviderDocumentList() async {
    Future<List<DocumentModel>> byType(String type) async {
      final snap = await _db
          .collection(CollectionName.documents)
          .where('type', isEqualTo: type)
          .where('enable', isEqualTo: true)
          .get();
      return snap.docs.map((d) => DocumentModel.fromJson(d.data())).toList();
    }

    var list = await byType('provider');
    if (list.isEmpty) list = await byType('ondemand');
    if (list.isEmpty) list = await byType('driver');
    return list;
  }

  static Future<ProviderDocumentModel?> getDocumentOfProvider() async {
    final uid = getCurrentUid();
    if (uid.isEmpty) return null;
    final snap = await _db.collection(CollectionName.documentsVerify).doc(uid).get();
    if (!snap.exists || snap.data() == null) return null;
    return ProviderDocumentModel.fromJson(snap.data()!);
  }

  static Future<String> uploadVerifyImage({required File file, required String docId, required String side}) async {
    final uid = getCurrentUid();
    final path = 'documents_verify/$uid/${docId}_$side.jpg';
    final ref = FirebaseStorage.instance.ref().child(path);
    await ref.putFile(file, SettableMetadata(contentType: 'image/jpeg'));
    return ref.getDownloadURL();
  }

  static Future<void> uploadProviderDocument(UploadedDocument document) async {
    final uid = getCurrentUid();
    final existing = await getDocumentOfProvider();
    final docs = List<UploadedDocument>.from(existing?.documents ?? const []);
    final index = docs.indexWhere((d) => d.documentId == document.documentId);
    if (index >= 0) {
      docs[index] = document;
    } else {
      docs.add(document);
    }
    final model = ProviderDocumentModel(
      id: uid,
      type: 'provider',
      pending: true,
      documents: docs,
    );
    await _db.collection(CollectionName.documentsVerify).doc(uid).set(model.toJson(), SetOptions(merge: true));
    await _db.collection(CollectionName.users).doc(uid).set({
      'isDocumentVerify': false,
    }, SetOptions(merge: true));
  }

  static Future<void> sendOrderChat({
    required String orderId,
    required String customerId,
    required String message,
  }) async {
    final uid = getCurrentUid();
    if (uid.isEmpty || orderId.isEmpty || message.trim().isEmpty) return;
    final threadId = const Uuid().v4();
    final inbox = {
      'senderId': uid,
      'receiverId': customerId,
      'lastSenderId': uid,
      'lastMessage': message,
      'lastMessageType': 'text',
      'orderId': orderId,
      'createdAt': Timestamp.now(),
      'chatType': Constant.userRoleProvider,
      'type': 'orderChat',
      'sender_receiver_id': [uid, customerId],
    };
    await _db.collection(CollectionName.chat).doc(orderId).set(inbox, SetOptions(merge: true));
    await _db.collection(CollectionName.chat).doc(orderId).collection('thread').doc(threadId).set({
      'id': threadId,
      'senderId': uid,
      'receiverId': customerId,
      'orderId': orderId,
      'message': message,
      'messageType': 'text',
      'createdAt': Timestamp.now(),
      'seen': false,
    });
  }

  static Stream<List<Map<String, dynamic>>> watchMyPayouts(String uid) {
    if (uid.isEmpty) return Stream.value(const []);
    return _db.collection(CollectionName.payouts).where('vendorID', isEqualTo: uid).snapshots().map((snap) {
      final list = snap.docs.map((d) => d.data()).toList();
      list.sort((a, b) {
        final at = a['paidDate'] is Timestamp ? (a['paidDate'] as Timestamp).millisecondsSinceEpoch : 0;
        final bt = b['paidDate'] is Timestamp ? (b['paidDate'] as Timestamp).millisecondsSinceEpoch : 0;
        return bt.compareTo(at);
      });
      return list;
    });
  }

  static Future<void> requestPayout({required double amount, required String note}) async {
    final uid = getCurrentUid();
    if (uid.isEmpty) throw Exception('Faça login novamente.');
    if (amount <= 0) throw Exception('Informe um valor válido.');
    final user = await getUserProfile(uid);
    final wallet = (user?.walletAmount ?? 0).toDouble();
    if (amount > wallet) throw Exception('Saldo insuficiente na carteira.');
    final id = Constant.getUuid();
    await _db.collection(CollectionName.payouts).doc(id).set({
      'id': id,
      'vendorID': uid,
      'amount': amount.toString(),
      'note': note,
      'paidDate': Timestamp.now(),
      'paymentStatus': 'Pending',
      'role': 'provider',
      'withdrawMethod': 'bank',
    });
    await updateUserWallet(amount: (-amount).toString(), userId: uid);
    await setWalletTransaction(
      WalletTransactionModel(
        id: Constant.getUuid(),
        amount: amount,
        date: Timestamp.now(),
        paymentMethod: 'wallet',
        transactionUser: 'provider',
        userId: uid,
        isTopup: false,
        note: note.isEmpty ? 'Solicitação de saque' : note,
        paymentStatus: 'pending',
      ),
    );
  }
}
