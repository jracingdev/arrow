import 'package:arrow_shared/report_strikes.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('incrementa strikes a partir de zero ou negativo', () {
    expect(ReportStrikes.increment(0), 1);
    expect(ReportStrikes.increment(-2), 1);
    expect(ReportStrikes.increment(2), 3);
  });

  test('recomenda banimento a partir de 3 strikes, sem auto-ban', () {
    expect(ReportStrikes.shouldRecommendBan(2), isFalse);
    expect(ReportStrikes.shouldRecommendBan(3), isTrue);
    expect(ReportStrikes.shouldRecommendBan(5), isTrue);
    expect(ReportStrikes.shouldRecommendBan(2, threshold: 2), isTrue);
  });

  test('conta ativa se active ou isActive for true', () {
    expect(UserActiveFlag.fromFields(active: true, isActive: false), isTrue);
    expect(UserActiveFlag.fromFields(active: false, isActive: true), isTrue);
    expect(UserActiveFlag.fromFields(active: false, isActive: false), isFalse);
    expect(UserActiveFlag.fromFields(), isFalse);
  });

  test('rótulos de categoria em português', () {
    expect(ReportCategories.labelPt(ReportCategories.noShow), 'Não compareceu');
    expect(ReportCategories.labelPt(ReportCategories.paymentFraud), contains('dinheiro'));
  });
}
