import 'package:arrow_shared/report_strikes.dart';
import 'package:flutter/material.dart';
import 'package:provider/themes/app_theme.dart';

Future<void> showReportProblemSheet({
  required BuildContext context,
  required Future<void> Function(String category, String description) onSubmit,
  bool sos = false,
}) {
  return showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
    builder: (ctx) => _ReportProblemSheet(onSubmit: onSubmit, sos: sos),
  );
}

class _ReportProblemSheet extends StatefulWidget {
  const _ReportProblemSheet({required this.onSubmit, required this.sos});

  final Future<void> Function(String category, String description) onSubmit;
  final bool sos;

  @override
  State<_ReportProblemSheet> createState() => _ReportProblemSheetState();
}

class _ReportProblemSheetState extends State<_ReportProblemSheet> {
  String _category = ReportCategories.abuse;
  final _description = TextEditingController();
  bool _busy = false;

  @override
  void dispose() {
    _description.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_description.text.trim().isEmpty) return;
    setState(() => _busy = true);
    try {
      await widget.onSubmit(_category, _description.text.trim());
      if (mounted) Navigator.of(context).pop();
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final inset = MediaQuery.of(context).viewInsets.bottom;
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 16, 20, 20 + inset),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              widget.sos ? 'Estou em risco / Denunciar agora' : 'Reportar problema',
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 8),
            const Text(
              'A plataforma analisa denúncias verificadas. Reincidência pode levar à suspensão da conta.',
              style: TextStyle(color: AppTheme.grey500, fontSize: 13),
            ),
            const SizedBox(height: 16),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                for (final category in ReportCategories.all)
                  ChoiceChip(
                    label: Text(ReportCategories.labelPt(category)),
                    selected: _category == category,
                    onSelected: (_) => setState(() => _category = category),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _description,
              maxLines: 4,
              decoration: const InputDecoration(
                labelText: 'Descreva o que aconteceu',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _busy ? null : _submit,
              style: widget.sos ? ElevatedButton.styleFrom(backgroundColor: AppTheme.danger) : null,
              child: Text(_busy ? 'Enviando...' : 'Enviar denúncia'),
            ),
          ],
        ),
      ),
    );
  }
}
