/// Regras da transação de aceite de chamado broadcast (sem I/O).
///
/// Espelha `FireStoreUtils.acceptBroadcast` no app Prestador:
/// só o primeiro prestador com `status == Order Placed` e sem `provider.author`
/// pode aceitar; quem já recusou (`rejectedBy`) não entra de novo.
class DispatchAcceptGuard {
  DispatchAcceptGuard._();

  static const orderPlaced = 'Order Placed';
  static const orderAccepted = 'Order Accepted';
  static const taken = 'taken';
  static const alreadyDeclined = 'already_declined';
  static const noUid = 'no_uid';

  static bool hasAssignedProvider(String? author) => (author ?? '').trim().isNotEmpty;

  /// `null` = pode aceitar; senão o código do erro da transação.
  static String? rejectReason({
    required String status,
    required String? assignedAuthor,
    required List<String> rejectedBy,
    required String uid,
  }) {
    if (uid.trim().isEmpty) return noUid;
    if (status != orderPlaced || hasAssignedProvider(assignedAuthor)) return taken;
    if (rejectedBy.contains(uid)) return alreadyDeclined;
    return null;
  }

  static bool canAccept({
    required String status,
    required String? assignedAuthor,
    required List<String> rejectedBy,
    required String uid,
  }) {
    return rejectReason(
          status: status,
          assignedAuthor: assignedAuthor,
          rejectedBy: rejectedBy,
          uid: uid,
        ) ==
        null;
  }

  /// Patch aplicado no doc `provider_orders` quando a transação passa.
  static Map<String, dynamic> acceptedPatch({
    required Map<String, dynamic> serviceSnapshot,
    required String uid,
    required String authorName,
    String authorProfilePic = '',
    String? phoneNumber,
  }) {
    final provider = Map<String, dynamic>.from(serviceSnapshot);
    provider['author'] = uid;
    provider['authorName'] = authorName;
    provider['authorProfilePic'] = authorProfilePic;
    if (phoneNumber != null) provider['phoneNumber'] = phoneNumber;
    return {
      'provider': provider,
      'status': orderAccepted,
    };
  }
}
