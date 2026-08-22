import 'package:arrow_shared/dispatch_accept.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  const uid = 'provider-uid';

  test('aceita broadcast só se ainda estiver Order Placed e sem prestador', () {
    expect(
      DispatchAcceptGuard.canAccept(
        status: 'Order Placed',
        assignedAuthor: '',
        rejectedBy: const [],
        uid: uid,
      ),
      isTrue,
    );
    expect(
      DispatchAcceptGuard.rejectReason(
        status: 'Order Accepted',
        assignedAuthor: '',
        rejectedBy: const [],
        uid: uid,
      ),
      DispatchAcceptGuard.taken,
    );
    expect(
      DispatchAcceptGuard.rejectReason(
        status: 'Order Placed',
        assignedAuthor: 'outro-prestador',
        rejectedBy: const [],
        uid: uid,
      ),
      DispatchAcceptGuard.taken,
    );
  });

  test('quem já recusou não aceita de novo; uid vazio falha', () {
    expect(
      DispatchAcceptGuard.rejectReason(
        status: 'Order Placed',
        assignedAuthor: '',
        rejectedBy: const [uid],
        uid: uid,
      ),
      DispatchAcceptGuard.alreadyDeclined,
    );
    expect(
      DispatchAcceptGuard.rejectReason(
        status: 'Order Placed',
        assignedAuthor: '',
        rejectedBy: const [],
        uid: '',
      ),
      DispatchAcceptGuard.noUid,
    );
  });

  test('patch de aceite preenche provider.author e status Order Accepted', () {
    final patch = DispatchAcceptGuard.acceptedPatch(
      serviceSnapshot: {'id': 'svc1', 'title': 'Limpeza residencial', 'author': ''},
      uid: uid,
      authorName: 'Joelson Justino',
      phoneNumber: '+5521999990000',
    );
    expect(patch['status'], 'Order Accepted');
    expect((patch['provider'] as Map)['author'], uid);
    expect((patch['provider'] as Map)['authorName'], 'Joelson Justino');
    expect((patch['provider'] as Map)['phoneNumber'], '+5521999990000');
    expect((patch['provider'] as Map)['title'], 'Limpeza residencial');
  });
}
