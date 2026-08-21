import 'dart:async';
import 'dart:io';

import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/order_invoice_model.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/worker_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';
import 'package:provider/widgets/hourly_timer_card.dart';
import 'package:url_launcher/url_launcher.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key, required this.orderId});

  final String orderId;

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  String? assignWorkerId;
  final otpController = TextEditingController();
  Timer? _tick;

  @override
  void initState() {
    super.initState();
    _tick = Timer.periodic(const Duration(seconds: 1), (_) {
      if (mounted) setState(() {});
    });
  }

  @override
  void dispose() {
    _tick?.cancel();
    otpController.dispose();
    super.dispose();
  }

  bool _scheduleBlocksStart(ProviderOrderModel order) {
    final when = order.newScheduleDateTime ?? order.scheduleDateTime;
    if (when == null) return false;
    return when.toDate().isAfter(DateTime.now());
  }

  Future<void> _accept(ProviderOrderModel order) async {
    ShowToastDialog.showLoader('Aceitando...');
    try {
      await FireStoreUtils.updateOrder(order.id, {
        'status': Constant.orderAccepted,
        'newScheduleDateTime': order.scheduleDateTime ?? Timestamp.now(),
      });
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Pedido aceito.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  Future<void> _reject(ProviderOrderModel order) async {
    ShowToastDialog.showLoader('Recusando...');
    try {
      await FireStoreUtils.updateOrder(order.id, {'status': Constant.orderRejected});
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Pedido recusado.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  Future<void> _assign(ProviderOrderModel order) async {
    ShowToastDialog.showLoader('Atribuindo...');
    try {
      await FireStoreUtils.updateOrder(order.id, {
        'status': Constant.orderAssigned,
        'workerId': assignWorkerId ?? '',
      });
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Pedido atribuído.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  Future<void> _start(ProviderOrderModel order) async {
    if (_scheduleBlocksStart(order)) {
      final when = order.newScheduleDateTime ?? order.scheduleDateTime;
      ShowToastDialog.showToast(
        'O atendimento está agendado para ${DateFormat('dd/MM/yyyy HH:mm').format(when!.toDate())}.',
      );
      return;
    }
    ShowToastDialog.showLoader('Iniciando atendimento...');
    try {
      final hourly = HourlyServiceBilling.isHourly(order.provider.priceUnit);
      await FireStoreUtils.updateOrder(order.id, {
        'status': Constant.orderOngoing,
        'startTime': FieldValue.serverTimestamp(),
        if (hourly) 'endTime': null,
      });
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(
        hourly ? 'Cronômetro iniciado. O tempo entra na cobrança por hora.' : 'Serviço em andamento.',
      );
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  Future<void> _stopTimer(ProviderOrderModel order) async {
    final start = order.startTime?.toDate();
    if (start == null) {
      ShowToastDialog.showToast('O atendimento ainda não começou.');
      return;
    }
    final hours = HourlyServiceBilling.billableHours(start, DateTime.now());
    ShowToastDialog.showLoader('Parando cronômetro...');
    try {
      await FireStoreUtils.updateOrder(order.id, {
        'endTime': FieldValue.serverTimestamp(),
        'quantity': hours,
      });
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Tempo registrado: ${hours.toStringAsFixed(2)} h.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  Future<void> _complete(ProviderOrderModel order) async {
    if (otpController.text.trim() != order.otp) {
      ShowToastDialog.showToast('Informe o OTP do cliente.');
      return;
    }
    ShowToastDialog.showLoader('Concluindo...');
    try {
      final hourly = HourlyServiceBilling.isHourly(order.provider.priceUnit);
      final payload = <String, dynamic>{
        'status': Constant.orderCompleted,
        'paymentStatus': hourly ? (order.paymentStatus ?? false) : true,
        'extraPaymentStatus': true,
      };
      if (hourly) {
        final start = order.startTime?.toDate();
        final end = order.endTime?.toDate() ?? DateTime.now();
        if (start != null) {
          payload['endTime'] = order.endTime ?? FieldValue.serverTimestamp();
          payload['quantity'] = HourlyServiceBilling.billableHours(start, end);
        }
      } else {
        payload['endTime'] = FieldValue.serverTimestamp();
      }
      await FireStoreUtils.updateOrder(order.id, payload);
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Pedido concluído.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Detalhe do pedido')),
      body: StreamBuilder<ProviderOrderModel?>(
        stream: FireStoreUtils.watchOrder(widget.orderId),
        builder: (context, snapshot) {
          final order = snapshot.data;
          if (order == null) {
            return const Center(child: CircularProgressIndicator());
          }
          final when = order.newScheduleDateTime ?? order.scheduleDateTime;
          return ListView(
            padding: const EdgeInsets.all(20),
            children: [
              Text(order.provider.title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              Text(Constant.statusLabel(order.status), style: const TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w600)),
              const SizedBox(height: 16),
              _row('Cliente', order.author.fullName()),
              _row('Telefone', order.author.phoneNumber ?? ''),
              _row('Pagamento', order.paymentMethod),
              if (when != null) _row('Agenda', DateFormat('dd/MM/yyyy HH:mm').format(when.toDate())),
              if (order.addressLine().isNotEmpty) _row('Endereço', order.addressLine()),
              if (order.notes.isNotEmpty) _row('Observações', order.notes),
              if (order.extraCharges.isNotEmpty && order.extraCharges != '0.0') _row('Taxa extra', order.extraCharges),
              _row('OTP', order.otp),
              const SizedBox(height: 20),
              if (order.status == Constant.orderPlaced) ...[
                ElevatedButton(onPressed: () => _accept(order), child: const Text('Aceitar')),
                const SizedBox(height: 8),
                OutlinedButton(
                  onPressed: () => _reject(order),
                  style: OutlinedButton.styleFrom(foregroundColor: AppTheme.danger),
                  child: const Text('Recusar'),
                ),
              ],
              if (order.status == Constant.orderAccepted) ...[
                StreamBuilder<List<WorkerModel>>(
                  stream: FireStoreUtils.watchMyWorkers(uid),
                  builder: (context, workersSnap) {
                    final workers = (workersSnap.data ?? []).where((w) => w.online).toList();
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Atribuir a', style: TextStyle(fontWeight: FontWeight.w600)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<String>(
                          initialValue: assignWorkerId ?? '',
                          decoration: const InputDecoration(border: OutlineInputBorder()),
                          items: [
                            const DropdownMenuItem(value: '', child: Text('Eu mesmo')),
                            ...workers.map((w) => DropdownMenuItem(value: w.id, child: Text(w.fullName()))),
                          ],
                          onChanged: (v) => setState(() => assignWorkerId = v),
                        ),
                        const SizedBox(height: 12),
                        ElevatedButton(onPressed: () => _assign(order), child: const Text('Atribuir')),
                      ],
                    );
                  },
                ),
              ],
              if (order.status == Constant.orderAssigned)
                ElevatedButton(onPressed: () => _start(order), child: const Text('Iniciar serviço')),
              if (order.status == Constant.orderOngoing || order.status == Constant.inTransit) ...[
                if (HourlyServiceBilling.isHourly(order.provider.priceUnit) && order.startTime != null) ...[
                  HourlyTimerCard(
                    elapsed: (order.endTime?.toDate() ?? DateTime.now()).difference(order.startTime!.toDate()),
                    rate: double.tryParse(order.provider.disPrice != '0' && order.provider.disPrice.isNotEmpty ? order.provider.disPrice : order.provider.price) ?? 0,
                    hours: HourlyServiceBilling.billableHours(
                      order.startTime!.toDate(),
                      order.endTime?.toDate() ?? DateTime.now(),
                    ),
                    running: order.endTime == null,
                  ),
                  if (order.endTime == null) ...[
                    const SizedBox(height: 12),
                    OutlinedButton(onPressed: () => _stopTimer(order), child: const Text('Parar cronômetro')),
                  ],
                  const SizedBox(height: 12),
                ],
                TextField(
                  controller: otpController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'OTP do cliente', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 12),
                ElevatedButton(onPressed: () => _complete(order), child: const Text('Concluir')),
              ],
              const SizedBox(height: 24),
              _invoicesSection(order),
            ],
          );
        },
      ),
    );
  }

  Widget _row(String label, String value) {
    if (value.isEmpty) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(width: 110, child: Text(label, style: const TextStyle(color: AppTheme.grey500))),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  Widget _invoicesSection(ProviderOrderModel order) {
    final canUpload = Constant.canUploadInvoice(order.status);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Documentos', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
        const SizedBox(height: 4),
        const Text('Nota fiscal (NFS-e)', style: TextStyle(color: AppTheme.grey500)),
        const SizedBox(height: 12),
        if (order.invoices.isEmpty)
          const Padding(
            padding: EdgeInsets.only(bottom: 12),
            child: Text('Nenhuma nota fiscal anexada.'),
          ),
        ...order.invoices.asMap().entries.map((entry) {
          final invoice = entry.value;
          final when = invoice.uploadedAt;
          return Card(
            margin: const EdgeInsets.only(bottom: 8),
            child: ListTile(
              leading: Icon(
                invoice.fileName.toLowerCase().endsWith('.pdf') ? Icons.picture_as_pdf : Icons.image,
                color: AppTheme.primary,
              ),
              title: Text(invoice.fileName.isEmpty ? 'NFS-e' : invoice.fileName),
              subtitle: when == null ? null : Text(DateFormat('dd/MM/yyyy HH:mm').format(when.toDate())),
              trailing: Wrap(
                spacing: 4,
                children: [
                  IconButton(
                    tooltip: 'Abrir',
                    icon: const Icon(Icons.open_in_new),
                    onPressed: () => _openInvoice(invoice),
                  ),
                  if (canUpload)
                    IconButton(
                      tooltip: 'Substituir',
                      icon: const Icon(Icons.swap_horiz),
                      onPressed: () => _pickAndUploadInvoice(order, replaceIndex: entry.key),
                    ),
                ],
              ),
            ),
          );
        }),
        if (canUpload)
          OutlinedButton.icon(
            onPressed: () => _pickAndUploadInvoice(order),
            icon: const Icon(Icons.attach_file),
            label: Text(order.invoices.isEmpty ? 'Anexar nota fiscal' : 'Anexar outra nota'),
          )
        else
          const Text(
            'O anexo fica disponível quando o serviço está em andamento ou concluído.',
            style: TextStyle(color: AppTheme.grey500, fontSize: 13),
          ),
      ],
    );
  }

  Future<void> _openInvoice(OrderInvoiceModel invoice) async {
    final uri = Uri.tryParse(invoice.url);
    if (uri == null || !await launchUrl(uri, mode: LaunchMode.externalApplication)) {
      ShowToastDialog.showToast('Não foi possível abrir o arquivo.');
    }
  }

  Future<void> _pickAndUploadInvoice(ProviderOrderModel order, {int? replaceIndex}) async {
    if (!Constant.canUploadInvoice(order.status)) {
      ShowToastDialog.showToast('Anexe a nota quando o serviço estiver em andamento ou concluído.');
      return;
    }
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: Constant.invoiceAllowedExtensions,
    );
    if (result == null || result.files.isEmpty) return;
    final picked = result.files.single;
    final path = picked.path;
    if (path == null || path.isEmpty) {
      ShowToastDialog.showToast('Não foi possível ler o arquivo.');
      return;
    }
    ShowToastDialog.showLoader(replaceIndex == null ? 'Enviando nota...' : 'Substituindo nota...');
    try {
      await FireStoreUtils.uploadOrderInvoice(
        orderId: order.id,
        file: File(path),
        fileName: picked.name,
        current: order.invoices,
        replaceIndex: replaceIndex,
      );
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(replaceIndex == null ? 'Nota fiscal anexada.' : 'Nota fiscal substituída.');
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }
}
