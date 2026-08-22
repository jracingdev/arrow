import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/constant/constant.dart';
import 'package:provider/models/user_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class CreateServiceScreen extends StatefulWidget {
  const CreateServiceScreen({super.key});

  @override
  State<CreateServiceScreen> createState() => _CreateServiceScreenState();
}

class _CreateServiceScreenState extends State<CreateServiceScreen> {
  final _title = TextEditingController();
  final _description = TextEditingController();
  final _price = TextEditingController();
  final _address = TextEditingController();

  String _priceUnit = 'Hourly';
  String _sectionId = '';
  String _categoryId = '';
  String _subCategoryId = '';
  bool _publish = true;
  bool _saving = false;
  bool _loading = true;
  String? _error;

  List<Map<String, dynamic>> _sections = const [];
  List<Map<String, dynamic>> _categories = const [];
  List<Map<String, dynamic>> _subCategories = const [];

  UserModel? get _user => Constant.userModel;

  @override
  void initState() {
    super.initState();
    _sectionId = _user?.sectionId ?? '';
    _load();
  }

  @override
  void dispose() {
    _title.dispose();
    _description.dispose();
    _price.dispose();
    _address.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final sections = await FireStoreUtils.getOnDemandSections();
      var sectionId = _sectionId;
      if (sectionId.isEmpty && sections.isNotEmpty) {
        sectionId = (sections.first['id'] ?? '').toString();
      }
      final categories = sectionId.isEmpty ? const <Map<String, dynamic>>[] : await FireStoreUtils.getProviderCategories(sectionId: sectionId);
      if (!mounted) return;
      setState(() {
        _sections = sections;
        _sectionId = sectionId;
        _categories = categories;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.toString();
      });
    }
  }

  Future<void> _onSection(String sectionId) async {
    setState(() {
      _sectionId = sectionId;
      _categoryId = '';
      _subCategoryId = '';
      _subCategories = const [];
    });
    final categories = await FireStoreUtils.getProviderCategories(sectionId: sectionId);
    if (!mounted) return;
    setState(() => _categories = categories);
  }

  Future<void> _onCategory(String categoryId) async {
    setState(() {
      _categoryId = categoryId;
      _subCategoryId = '';
    });
    final subs = await FireStoreUtils.getProviderCategories(sectionId: _sectionId, parentCategoryId: categoryId);
    if (!mounted) return;
    setState(() => _subCategories = subs);
  }

  Future<void> _save() async {
    final user = _user;
    if (user == null) {
      setState(() => _error = 'Faça login novamente.');
      return;
    }
    setState(() {
      _saving = true;
      _error = null;
    });
    try {
      await FireStoreUtils.createMyService(
        user: user,
        title: _title.text,
        description: _description.text,
        price: _price.text,
        priceUnit: _priceUnit,
        sectionId: _sectionId,
        categoryId: _categoryId,
        subCategoryId: _subCategoryId,
        publish: _publish,
        address: _address.text,
        latitude: user.latitude,
        longitude: user.longitude,
      );
      if (!mounted) return;
      Get.back();
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _error = e.toString().replaceFirst('Exception: ', '');
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Novo serviço')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                if (_error != null)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: Text(_error!, style: const TextStyle(color: AppTheme.danger)),
                  ),
                TextField(
                  controller: _title,
                  decoration: const InputDecoration(labelText: 'Título', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  key: ValueKey('section-$_sectionId'),
                  initialValue: _sectionId.isEmpty ? null : _sectionId,
                  decoration: const InputDecoration(labelText: 'Seção', border: OutlineInputBorder()),
                  items: _sections
                      .map((s) => DropdownMenuItem(value: (s['id'] ?? '').toString(), child: Text((s['name'] ?? s['id'] ?? '').toString())))
                      .toList(),
                  onChanged: _saving ? null : (v) {
                    if (v != null) _onSection(v);
                  },
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  key: ValueKey('category-$_categoryId'),
                  initialValue: _categoryId.isEmpty ? null : _categoryId,
                  decoration: const InputDecoration(labelText: 'Categoria', border: OutlineInputBorder()),
                  items: _categories
                      .map((c) => DropdownMenuItem(value: (c['id'] ?? '').toString(), child: Text((c['title'] ?? c['id'] ?? '').toString())))
                      .toList(),
                  onChanged: _saving ? null : (v) {
                    if (v != null) _onCategory(v);
                  },
                ),
                if (_subCategories.isNotEmpty) ...[
                  const SizedBox(height: 12),
                  DropdownButtonFormField<String>(
                    key: ValueKey('sub-$_subCategoryId'),
                    initialValue: _subCategoryId.isEmpty ? null : _subCategoryId,
                    decoration: const InputDecoration(labelText: 'Subcategoria (opcional)', border: OutlineInputBorder()),
                    items: [
                      const DropdownMenuItem(value: '', child: Text('Nenhuma')),
                      ..._subCategories.map((c) => DropdownMenuItem(value: (c['id'] ?? '').toString(), child: Text((c['title'] ?? c['id'] ?? '').toString()))),
                    ],
                    onChanged: _saving ? null : (v) => setState(() => _subCategoryId = v ?? ''),
                  ),
                ],
                const SizedBox(height: 12),
                TextField(
                  controller: _price,
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  decoration: const InputDecoration(labelText: 'Preço', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  initialValue: _priceUnit,
                  decoration: const InputDecoration(labelText: 'Unidade', border: OutlineInputBorder()),
                  items: const [
                    DropdownMenuItem(value: 'Hourly', child: Text('Por hora')),
                    DropdownMenuItem(value: 'Fixed', child: Text('Preço fixo')),
                  ],
                  onChanged: _saving ? null : (v) => setState(() => _priceUnit = v ?? 'Hourly'),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _description,
                  maxLines: 3,
                  decoration: const InputDecoration(labelText: 'Descrição', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _address,
                  decoration: const InputDecoration(
                    labelText: 'Endereço (opcional)',
                    helperText: 'A localização publicada usa a do seu perfil.',
                    border: OutlineInputBorder(),
                  ),
                ),
                SwitchListTile(
                  contentPadding: EdgeInsets.zero,
                  title: const Text('Publicar agora'),
                  subtitle: const Text('Clientes só veem serviços com publish = true.'),
                  value: _publish,
                  onChanged: _saving ? null : (v) => setState(() => _publish = v),
                ),
                const SizedBox(height: 8),
                ElevatedButton(
                  onPressed: _saving ? null : _save,
                  child: Text(_saving ? 'Salvando…' : 'Criar e publicar'),
                ),
              ],
            ),
    );
  }
}
