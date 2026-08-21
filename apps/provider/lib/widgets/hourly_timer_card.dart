import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:flutter/material.dart';
import 'package:provider/themes/app_theme.dart';

class HourlyTimerCard extends StatelessWidget {
  const HourlyTimerCard({
    super.key,
    required this.elapsed,
    required this.rate,
    required this.hours,
    this.running = true,
  });

  final Duration elapsed;
  final double rate;
  final double hours;
  final bool running;

  @override
  Widget build(BuildContext context) {
    final total = HourlyServiceBilling.amount(rate, hours);
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF111827),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          Text(
            running ? 'Tempo em atendimento' : 'Tempo faturado',
            style: const TextStyle(color: Colors.white70, fontSize: 13),
          ),
          const SizedBox(height: 6),
          Text(
            HourlyServiceBilling.formatElapsed(elapsed),
            style: const TextStyle(
              color: Colors.white,
              fontSize: 40,
              fontWeight: FontWeight.w700,
              letterSpacing: 1.5,
              fontFeatures: [FontFeature.tabularFigures()],
            ),
          ),
          const SizedBox(height: 8),
          Text(
            '${_money(rate)}/hora · ${hours.toStringAsFixed(2)} h · ${_money(total)}',
            style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w600),
          ),
          if (running)
            const Padding(
              padding: EdgeInsets.only(top: 8),
              child: Text(
                'O cliente vê este tempo no app. Mínimo de 1 hora na cobrança.',
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.white54, fontSize: 12),
              ),
            ),
        ],
      ),
    );
  }

  String _money(double value) {
    return 'R\$ ${value.toStringAsFixed(2).replaceAll('.', ',')}';
  }
}
