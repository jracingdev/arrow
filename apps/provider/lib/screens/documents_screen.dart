import 'dart:io';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:provider/constant/show_toast_dialog.dart';
import 'package:provider/models/document_model.dart';
import 'package:provider/models/provider_document_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class DocumentsScreen extends StatefulWidget {
  const DocumentsScreen({super.key});

  @override
  State<DocumentsScreen> createState() => _DocumentsScreenState();
}

class _DocumentsScreenState extends State<DocumentsScreen> {
  bool loading = true;
  List<DocumentModel> catalog = [];
  ProviderDocumentModel? uploaded;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    catalog = await FireStoreUtils.getProviderDocumentList();
    uploaded = await FireStoreUtils.getDocumentOfProvider();
    if (mounted) setState(() => loading = false);
  }

  UploadedDocument? _uploadedFor(String? docId) {
    if (docId == null) return null;
    final docs = uploaded?.documents ?? const [];
    for (final d in docs) {
      if (d.documentId == docId) return d;
    }
    return null;
  }

  String _statusLabel(UploadedDocument? doc) {
    final status = (doc?.status ?? '').toLowerCase();
    if (status == 'approved') return 'Aprovado';
    if (status == 'rejected') return 'Recusado';
    if (doc?.frontImage?.isNotEmpty == true) return 'Pendente de aprovação';
    return 'Não enviado';
  }

  String? _rejectReason(UploadedDocument? doc) {
    if ((doc?.status ?? '').toLowerCase() != 'rejected') return null;
    final reason = doc?.rejectReason?.trim();
    if (reason != null && reason.isNotEmpty) return reason;
    final parent = uploaded?.latestRejectReason;
    return parent;
  }

  Future<void> _pickAndUpload(DocumentModel doc, {required bool back}) async {
    final result = await FilePicker.platform.pickFiles(type: FileType.image);
    if (result == null || result.files.isEmpty) return;
    final path = result.files.single.path;
    if (path == null || path.isEmpty) {
      ShowToastDialog.showToast('Não foi possível ler a imagem.');
      return;
    }
    ShowToastDialog.showLoader('Enviando documento...');
    try {
      final current = _uploadedFor(doc.id) ?? UploadedDocument(documentId: doc.id, status: 'pending');
      final url = await FireStoreUtils.uploadVerifyImage(
        file: File(path),
        docId: doc.id ?? 'doc',
        side: back ? 'back' : 'front',
      );
      if (back) {
        current.backImage = url;
      } else {
        current.frontImage = url;
      }
      current.documentId = doc.id;
      current.status = 'pending';
      current.rejectReason = '';
      await FireStoreUtils.uploadProviderDocument(current);
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast('Documento enviado. Aguarde a aprovação do administrador.');
      await _load();
    } catch (e) {
      ShowToastDialog.closeLoader();
      ShowToastDialog.showToast(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Documentos')),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : catalog.isEmpty
              ? const Center(
                  child: Padding(
                    padding: EdgeInsets.all(24),
                    child: Text(
                      'Nenhum documento obrigatório cadastrado. O administrador precisa criar documentos do tipo prestador.',
                      textAlign: TextAlign.center,
                    ),
                  ),
                )
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    const Text(
                      'Envie os documentos para verificação. O login continua liberado até o administrador aprovar.',
                      style: TextStyle(color: AppTheme.grey500),
                    ),
                    if (uploaded?.hasRejected == true && uploaded?.latestRejectReason != null) ...[
                      const SizedBox(height: 12),
                      Text('Recusado. Motivo: ${uploaded!.latestRejectReason}', style: const TextStyle(color: Color(0xFFB91C1C))),
                    ],
                    const SizedBox(height: 16),
                    for (final doc in catalog) _card(doc),
                  ],
                ),
    );
  }

  Widget _card(DocumentModel doc) {
    final current = _uploadedFor(doc.id);
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(doc.title ?? 'Documento', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
            const SizedBox(height: 4),
            Text(_statusLabel(current), style: const TextStyle(color: AppTheme.grey500)),
            if (_rejectReason(current) != null) ...[
              const SizedBox(height: 6),
              Text('Motivo: ${_rejectReason(current)}', style: const TextStyle(color: Color(0xFFB91C1C))),
            ],
            const SizedBox(height: 12),
            if (doc.frontSide != false)
              OutlinedButton.icon(
                onPressed: () => _pickAndUpload(doc, back: false),
                icon: const Icon(Icons.upload_file),
                label: Text(current?.frontImage?.isNotEmpty == true ? 'Substituir frente' : 'Enviar frente'),
              ),
            if (doc.backSide == true) ...[
              const SizedBox(height: 8),
              OutlinedButton.icon(
                onPressed: () => _pickAndUpload(doc, back: true),
                icon: const Icon(Icons.upload_file),
                label: Text(current?.backImage?.isNotEmpty == true ? 'Substituir verso' : 'Enviar verso'),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
