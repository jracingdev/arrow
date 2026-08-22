/// Rótulos de UI em pt-BR alinhados ao website (`ArrowI18n.dayLabel`).
class ArrowI18n {
  ArrowI18n._();

  static const dayMap = <String, String>{
    'Sunday': 'Domingo',
    'Monday': 'Segunda-feira',
    'Tuesday': 'Terça-feira',
    'Wednesday': 'Quarta-feira',
    'Thursday': 'Quinta-feira',
    'Friday': 'Sexta-feira',
    'Saturday': 'Sábado',
    'Sun': 'Dom',
    'Mon': 'Seg',
    'Tue': 'Ter',
    'Wed': 'Qua',
    'Thu': 'Qui',
    'Fri': 'Sex',
    'Sat': 'Sáb',
  };

  static String dayLabel(dynamic day) {
    if (day == null) return '';
    final key = day.toString().trim();
    if (key.isEmpty) return '';
    return dayMap[key] ?? dayMap[_titleCase(key)] ?? key;
  }

  static String _titleCase(String value) {
    if (value.isEmpty) return value;
    return '${value[0].toUpperCase()}${value.substring(1).toLowerCase()}';
  }
}
