import 'package:arrow_shared/arrow_i18n.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('dayLabel traduz dias em inglês como o website', () {
    expect(ArrowI18n.dayLabel('Monday'), 'Segunda-feira');
    expect(ArrowI18n.dayLabel('Tuesday'), 'Terça-feira');
    expect(ArrowI18n.dayLabel('Wednesday'), 'Quarta-feira');
    expect(ArrowI18n.dayLabel('Thursday'), 'Quinta-feira');
    expect(ArrowI18n.dayLabel('Friday'), 'Sexta-feira');
    expect(ArrowI18n.dayLabel('Saturday'), 'Sábado');
    expect(ArrowI18n.dayLabel('Sunday'), 'Domingo');
    expect(ArrowI18n.dayLabel('monday'), 'Segunda-feira');
    expect(ArrowI18n.dayLabel(''), '');
  });
}
