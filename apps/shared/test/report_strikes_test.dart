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
    expect(ReportCategories.labelPt(ReportCategories.noShow), 'Prestador não compareceu');
    expect(ReportCategories.labelPt(ReportCategories.noShow, role: 'provider'), 'Cliente não estava no local');
    expect(ReportCategories.labelPt(ReportCategories.paymentFraud), contains('dinheiro'));
    expect(ReportCategories.labelPt(ReportCategories.paymentDispute, role: 'provider'), contains('pagar'));
    expect(ReportCategories.labelPt(ReportCategories.unsafeSituation), 'Situação de risco');
  });

  test('categorias de denúncia mudam por papel', () {
    expect(ReportCategories.forProvider, isNot(contains(ReportCategories.badService)));
    expect(ReportCategories.forProvider, isNot(contains(ReportCategories.paymentFraud)));
    expect(ReportCategories.forProvider, contains(ReportCategories.unsafeSituation));
    expect(ReportCategories.forProvider, contains(ReportCategories.paymentDispute));
    expect(ReportCategories.forCustomer, contains(ReportCategories.badService));
    expect(ReportCategories.forCustomer, contains(ReportCategories.paymentFraud));
    expect(ReportCategories.forCustomer, isNot(contains(ReportCategories.unsafeSituation)));
    expect(ReportCategories.forCustomer, isNot(contains(ReportCategories.paymentDispute)));
    expect(ReportCategories.forRole('provider'), ReportCategories.forProvider);
    expect(ReportCategories.forRole('customer'), ReportCategories.forCustomer);
  });
}
