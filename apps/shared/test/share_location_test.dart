import 'package:arrow_shared/share_location.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('monta link do mapa sem chave e ignora coordenada vazia', () {
    expect(
      ShareLocationMessage.mapsUrl(lat: -23.5505, lng: -46.6333),
      'https://www.google.com/maps/search/?api=1&query=-23.5505,-46.6333',
    );
    expect(ShareLocationMessage.mapsUrl(lat: 0, lng: 0), '');
    expect(ShareLocationMessage.mapsUrl(), '');
  });

  test('texto do cliente inclui endereço, horário e mapa', () {
    final text = ShareLocationMessage.build(
      who: 'João Prestador',
      address: 'Rua das Flores, 10',
      schedule: '22/08/2026 14:00',
      lat: -23.55,
      lng: -46.63,
      liveEnRoute: true,
    );
    expect(text, contains('Compartilhei o local do meu atendimento Arrow.'));
    expect(text, contains('Quem: João Prestador'));
    expect(text, contains('Endereço: Rua das Flores, 10'));
    expect(text, contains('Horário: 22/08/2026 14:00'));
    expect(text, contains('O profissional está a caminho ou no local.'));
    expect(text, contains('https://www.google.com/maps/search/?api=1&query=-23.55,-46.63'));
  });

  test('texto do prestador e job ativo', () {
    expect(ShareLocationMessage.isLiveJob('Order Ongoing'), isTrue);
    expect(ShareLocationMessage.isLiveJob('Order Placed'), isFalse);
    expect(
      ShareLocationMessage.build(who: 'Maria', address: 'Av. Brasil', fromProvider: true),
      contains('Estou em um atendimento Arrow.'),
    );
  });
}
