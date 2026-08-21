import 'package:arrow_shared/arrow_production_config.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/constant/collection_name.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/firebase_options.dart';

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
    expect(Constant.tabCancelled, contains('Driver Rejected'));
    expect(CollectionName.providersServices, 'providers_services');
    expect(Constant.statusLabel(Constant.orderPlaced), 'Pedido realizado');
    expect(Constant.statusLabel(Constant.orderAccepted), 'Pedido aceito');
    expect(Constant.statusLabel(Constant.orderAssigned), 'Pedido atribuído');
    expect(Constant.statusLabel(Constant.orderOngoing), 'Em andamento');
    expect(Constant.statusLabel(Constant.orderCompleted), 'Pedido concluído');
  });
}
