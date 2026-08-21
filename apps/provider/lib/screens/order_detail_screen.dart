import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/provider_order_model.dart';
import 'package:provider/models/worker_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key, required this.orderId});

  final String orderId;

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  String? assignWorkerId;
  final otpController = TextEditingController();

  @override
  void dispose() {
    otpController.dispose();
    super.dispose();
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
    ShowToastDialog.showLoader('Iniciando...');
    try {
      await FireStoreUtils.updateOrder(order.id, {
        'status': Constant.orderOngoing,
        'startTime': FieldValue.serverTimestamp(),
      });
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Serviço em andamento.');
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
      await FireStoreUtils.updateOrder(order.id, {
        'status': Constant.orderCompleted,
        'paymentStatus': true,
        'extraPaymentStatus': true,
        'endTime': FieldValue.serverTimestamp(),
      });
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
                TextField(
                  controller: otpController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'OTP do cliente', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 12),
                ElevatedButton(onPressed: () => _complete(order), child: const Text('Concluir')),
              ],
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
}
