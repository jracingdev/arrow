import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/constant/collection_name.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

/// Chat do pedido no mesmo protocolo do app cliente (`chat/{orderId}/thread`).
class ChatScreen extends StatefulWidget {
  const ChatScreen({
    super.key,
    required this.orderId,
    required this.customerId,
    required this.customerName,
  });

  final String orderId;
  final String customerId;
  final String customerName;

  @override
  State<ChatScreen> createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final controller = TextEditingController();
  final scroll = ScrollController();

  @override
  void dispose() {
    controller.dispose();
    scroll.dispose();
    super.dispose();
  }

  Future<void> _send() async {
    final text = controller.text.trim();
    if (text.isEmpty) return;
    controller.clear();
    try {
      await FireStoreUtils.sendOrderChat(
        orderId: widget.orderId,
        customerId: widget.customerId,
        message: text,
      );
    } catch (e) {
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: Text(widget.customerName.isEmpty ? 'Chat' : widget.customerName)),
      body: Column(
        children: [
          Expanded(
            child: StreamBuilder<QuerySnapshot<Map<String, dynamic>>>(
              stream: FirebaseFirestore.instance
                  .collection(CollectionName.chat)
                  .doc(widget.orderId)
                  .collection('thread')
                  .orderBy('createdAt')
                  .snapshots(),
              builder: (context, snapshot) {
                final docs = snapshot.data?.docs ?? const [];
                if (docs.isEmpty) {
                  return const Center(child: Text('Nenhuma mensagem ainda.'));
                }
                return ListView.builder(
                  controller: scroll,
                  padding: const EdgeInsets.all(16),
                  itemCount: docs.length,
                  itemBuilder: (context, i) {
                    final data = docs[i].data();
                    final mine = data['senderId']?.toString() == uid;
                    final message = data['message']?.toString() ?? '';
                    final when = data['createdAt'] is Timestamp ? (data['createdAt'] as Timestamp).toDate() : null;
                    return Align(
                      alignment: mine ? Alignment.centerRight : Alignment.centerLeft,
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 8),
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
                        decoration: BoxDecoration(
                          color: mine ? AppTheme.primary : AppTheme.grey50,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(message, style: TextStyle(color: mine ? Colors.white : AppTheme.grey900)),
                            if (when != null)
                              Text(
                                DateFormat('HH:mm').format(when),
                                style: TextStyle(fontSize: 11, color: mine ? Colors.white70 : AppTheme.grey500),
                              ),
                          ],
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(12, 8, 12, 8),
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: controller,
                      textInputAction: TextInputAction.send,
                      onSubmitted: (_) => _send(),
                      decoration: const InputDecoration(
                        hintText: 'Mensagem',
                        border: OutlineInputBorder(),
                        isDense: true,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton.filled(
                    onPressed: _send,
                    style: IconButton.styleFrom(backgroundColor: AppTheme.primary),
                    icon: const Icon(Icons.send, color: Colors.white),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
