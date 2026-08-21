import 'dart:io';

import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_storage/firebase_storage.dart';
import 'package:provider/constant/collection_name.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/order_invoice_model.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/models/user_model.dart';
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
}
