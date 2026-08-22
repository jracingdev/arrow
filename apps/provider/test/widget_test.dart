import 'package:arrow_shared/arrow_production_config.dart';
import 'package:arrow_shared/document_verification.dart';
import 'package:arrow_shared/rating_average.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/constant/collection_name.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/firebase_options.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/service/fire_store_utils.dart';

void main() {
  test('package Android do prestador vem do shared', () {
    expect(ArrowAndroidPackages.provider, 'br.app.arrow.provider');
    expect(Constant.androidPackage, ArrowAndroidPackages.provider);
  });

  test('coleções Firestore on-demand vêm do shared', () {
    expect(CollectionName.providerOrders, ArrowFirestoreCollections.providerOrders);
    expect(CollectionName.providersWorkers, ArrowFirestoreCollections.providersWorkers);
  });

  test('App ID Firebase Android do prestador vem do shared e do JSON oficial', () {
    expect(ArrowFirebaseAndroidAppIds.provider, '1:661081769489:android:e3046689b04067d2a4d3b0');
    expect(DefaultFirebaseOptions.androidAppId, ArrowFirebaseAndroidAppIds.provider);
    expect(DefaultFirebaseOptions.android.appId, DefaultFirebaseOptions.androidAppId);
    expect(DefaultFirebaseOptions.android.apiKey, 'AIzaSyCkfDofdVDF8BpZ3KC7yuO6D9gznmC4m7E');
    expect(DefaultFirebaseOptions.android.projectId, 'j-arrow');
  });

  test('status Firestore iguais ao painel store.arrow.app.br', () {
    expect(Constant.orderPlaced, 'Order Placed');
    expect(Constant.orderAccepted, 'Order Accepted');
    expect(Constant.orderAssigned, 'Order Assigned');
    expect(Constant.orderOngoing, 'Order Ongoing');
    expect(Constant.orderCompleted, 'Order Completed');
    expect(Constant.orderRejected, 'Order Rejected');
    expect(Constant.orderCancelled, 'Order Cancelled');
    expect(Constant.tabActive, contains('In Transit'));
    expect(Constant.tabAccepted, contains('Order Accepted'));
    expect(Constant.tabOngoing, contains('Order Ongoing'));
    expect(Constant.tabUpcoming, contains('Order Assigned'));
    expect(Constant.paymentLabel(method: 'cod', paid: false), 'Dinheiro · A pagar');
    expect(Constant.paymentLabel(method: 'stripe', paid: true), 'Pago · Cartão');
    expect(Constant.amountShow(amount: '80'), contains('R\$'));
    expect(CollectionName.providersServices, 'providers_services');
    expect(CollectionName.wallet, 'wallet');
    expect(CollectionName.documentsVerify, 'documents_verify');
    expect(CollectionName.itemsReview, 'items_review');
    expect(CollectionName.chat, 'chat');
    expect(CollectionName.payouts, 'payouts');
    expect(CollectionName.complaints, 'complaints');
    expect(CollectionName.sos, 'SOS');
    expect(Constant.statusLabel(Constant.orderPlaced), 'Pedido realizado');
    expect(Constant.statusLabel(Constant.orderAccepted), 'Pedido aceito');
    expect(Constant.statusLabel(Constant.orderAssigned), 'Pedido atribuído');
    expect(Constant.statusLabel(Constant.orderOngoing), 'Em andamento');
    expect(Constant.statusLabel(Constant.orderCompleted), 'Pedido concluído');
    expect(Constant.canUploadInvoice(Constant.orderOngoing), isTrue);
    expect(Constant.canUploadInvoice(Constant.orderCompleted), isTrue);
    expect(Constant.canUploadInvoice(Constant.orderPlaced), isFalse);
    expect(Constant.invoiceTypeNfse, 'nfs-e');
    expect(Constant.dispatchBroadcast, 'broadcast');
    expect(Constant.dispatchDirect, 'direct');
  });

  test('prestador disponível usa users.online', () {
    expect(UserModel(online: true).online, isTrue);
    expect(UserModel.fromJson({'online': true}).online, isTrue);
    expect(UserModel.fromJson({}).online, isFalse);
    expect(UserModel.fromJson({'online': true}).toJson()['online'], isTrue);
  });

  test('path de storage da NFS-e da reserva', () {
    expect(
      FireStoreUtils.invoiceStoragePath('order123', 'file-uuid', 'pdf'),
      'provider_orders/order123/invoices/file-uuid.pdf',
    );
  });

  test('selo público só após aprovação do admin', () {
    expect(DocumentVerification.isVerifiedForPublic(isDocumentVerify: true), isTrue);
    expect(DocumentVerification.isVerifiedForPublic(isAutoVerify: true), isFalse);
  });

  test('payload publicado liga o prestador ao serviço que o cliente lista', () {
    final map = FireStoreUtils.publishedServiceFields(
      author: 'provider-uid',
      title: 'Limpeza residencial',
      sectionId: 'ondemand-br',
      categoryId: 'limpeza',
      price: '80',
      priceUnit: 'Hourly',
      publish: true,
    );
    expect(map['author'], 'provider-uid');
    expect(map['publish'], isTrue);
    expect(map['sectionId'], 'ondemand-br');
    expect(map['categoryId'], 'limpeza');
    expect(map['priceUnit'], 'Hourly');
    expect(CollectionName.providersServices, 'providers_services');
  });

  test('média de avaliações e rótulo de pagamento', () {
    expect(RatingAverage.of(9, 2), 4.5);
    expect(RatingAverage.formatted(10, 2), '5.0');
    expect(Constant.isCod('cod'), isTrue);
    expect(Constant.paymentLabel(method: 'wallet', paid: true), contains('Pago'));
    expect(Constant.paymentLabel(method: 'cod', paid: false), contains('A pagar'));
  });
}
