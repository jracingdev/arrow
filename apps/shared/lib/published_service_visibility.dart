/// Visibilidade de `providers_services` no app do cliente.
///
/// O site lista documentos publicados. O app escondia tudo quando a comissão
/// estava ligada e o serviço não copiava `subscription_plan` do prestador.
class PublishedServiceVisibility {
  PublishedServiceVisibility._();

  static const unlimited = 1 << 20;

  static int itemCap(String? itemLimit) {
    if (itemLimit == null || itemLimit.trim().isEmpty || itemLimit == '-1') {
      return unlimited;
    }
    return int.tryParse(itemLimit) ?? unlimited;
  }

  static bool includePublishedAt({required int index, String? itemLimit}) {
    return index < itemCap(itemLimit);
  }

  /// Comissão sozinha não esconde serviço já publicado (paridade com o site).
  static bool passesListing({
    required bool subscriptionModelApplied,
    required bool hasPlan,
    required bool expired,
    String? totalOrders,
  }) {
    if (!subscriptionModelApplied) return true;
    if (!hasPlan) return true;
    if (expired) return false;
    if (totalOrders == '0') return false;
    return true;
  }

  static List<T> takeByAuthorItemLimit<T>({
    required Iterable<T> services,
    required String? Function(T service) authorOf,
    required String? Function(T service) itemLimitOf,
    int Function(T a, T b)? compareCreated,
  }) {
    final byAuthor = <String, List<T>>{};
    for (final service in services) {
      byAuthor.putIfAbsent(authorOf(service) ?? '', () => <T>[]).add(service);
    }
    final out = <T>[];
    for (final list in byAuthor.values) {
      if (compareCreated != null) {
        list.sort(compareCreated);
      }
      for (var i = 0; i < list.length; i++) {
        if (includePublishedAt(index: i, itemLimit: itemLimitOf(list[i]))) {
          out.add(list[i]);
        }
      }
    }
    return out;
  }
}
