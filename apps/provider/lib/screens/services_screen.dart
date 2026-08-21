import 'package:flutter/material.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class ServicesScreen extends StatelessWidget {
  const ServicesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Serviços')),
      body: StreamBuilder<List<ProviderServiceModel>>(
        stream: FireStoreUtils.watchMyServices(uid),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }
          final services = snapshot.data ?? [];
          if (services.isEmpty) {
            return const Center(child: Text('Nenhum serviço cadastrado.'));
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: services.length,
            separatorBuilder: (_, _) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final service = services[i];
              final price = service.disPrice.isNotEmpty && service.disPrice != '0' ? service.disPrice : service.price;
              return SwitchListTile(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: AppTheme.grey200),
                ),
                title: Text(service.title),
                subtitle: Text('$price ${service.priceUnit}'),
                value: service.publish,
                onChanged: (value) => FireStoreUtils.setServicePublish(service.id, value),
              );
            },
          );
        },
      ),
    );
  }
}
