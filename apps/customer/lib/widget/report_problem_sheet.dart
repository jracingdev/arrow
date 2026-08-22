import 'package:arrow_shared/report_strikes.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../themes/app_them_data.dart';

Future<void> showOnDemandReportSheet({
  required Future<void> Function(String category, String description) onSubmit,
  String role = 'customer',
}) {
  return Get.bottomSheet(
    _OnDemandReportSheet(onSubmit: onSubmit, role: role),
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
  );
}

class _OnDemandReportSheet extends StatefulWidget {
  const _OnDemandReportSheet({required this.onSubmit, required this.role});

  final Future<void> Function(String category, String description) onSubmit;
  final String role;

  @override
  State<_OnDemandReportSheet> createState() => _OnDemandReportSheetState();
}

class _OnDemandReportSheetState extends State<_OnDemandReportSheet> {
  String _category = ReportCategories.abuse;
  final _description = TextEditingController();
  bool _busy = false;

  @override
  void dispose() {
    _description.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_description.text.trim().isEmpty) {
      return;
    }
    setState(() => _busy = true);
    try {
      await widget.onSubmit(_category, _description.text.trim());
      if (Get.isBottomSheetOpen == true) Get.back();
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.fromLTRB(20, 16, 20, 20 + MediaQuery.of(context).viewInsets.bottom),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Reportar problema'.tr,
              style: AppThemeData.boldTextStyle(fontSize: 18, color: AppThemeData.grey900),
            ),
            const SizedBox(height: 8),
            Text(
              'Denúncias verificadas e reincidentes podem levar à suspensão da conta.'.tr,
              style: AppThemeData.regularTextStyle(fontSize: 13, color: AppThemeData.grey500),
            ),
            const SizedBox(height: 16),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                for (final category in ReportCategories.forRole(widget.role))
                  ChoiceChip(
                    label: Text(ReportCategories.labelPt(category, role: widget.role).tr),
                    selected: _category == category,
                    onSelected: (_) => setState(() => _category = category),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _description,
              maxLines: 4,
              decoration: InputDecoration(
                labelText: 'Descreva o que aconteceu'.tr,
                border: const OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _busy ? null : _submit,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppThemeData.primary300,
                minimumSize: const Size.fromHeight(48),
              ),
              child: Text(
                _busy ? 'Enviando...'.tr : 'Enviar denúncia'.tr,
                style: const TextStyle(color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
