import 'package:arrow_shared/hourly_service_billing.dart';
import 'package:arrow_shared/rating_average.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:provider/models/provider_service_model.dart';
import 'package:provider/screens/create_service_screen.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class ServicesScreen extends StatelessWidget {
  const ServicesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Serviços')),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Get.to(() => const CreateServiceScreen()),
        icon: const Icon(Icons.add),
        label: const Text('Novo serviço'),
      ),
      body: StreamBuilder<List<ProviderServiceModel>>(
        stream: FireStoreUtils.watchMyServices(uid),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }
          final services = snapshot.data ?? [];
          if (services.isEmpty) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Text('Nenhum serviço cadastrado.', textAlign: TextAlign.center),
                    const SizedBox(height: 8),
                    const Text(
                      'Clientes só veem documentos publicados em providers_services com o seu uid.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: AppTheme.grey500, fontSize: 13),
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () => Get.to(() => const CreateServiceScreen()),
                      child: const Text('Criar e publicar serviço'),
                    ),
                  ],
                ),
              ),
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: services.length,
            separatorBuilder: (_, _) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final service = services[i];
              final price = service.disPrice.isNotEmpty && service.disPrice != '0' ? service.disPrice : service.price;
              final unit = HourlyServiceBilling.isHourly(service.priceUnit) ? '/hora' : '';
              final avg = RatingAverage.formatted(service.reviewsSum, service.reviewsCount);
              return SwitchListTile(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: AppTheme.grey200),
                ),
                title: Text(service.title),
                subtitle: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('$price$unit'),
                    Row(
                      children: [
                        for (var s = 1; s <= 5; s++)
                          Icon(
                            s <= RatingAverage.of(service.reviewsSum, service.reviewsCount).round()
                                ? Icons.star
                                : Icons.star_border,
                            size: 14,
                            color: AppTheme.warning,
                          ),
                        const SizedBox(width: 6),
                        Text('$avg (${service.reviewsCount})', style: const TextStyle(color: AppTheme.grey500, fontSize: 12)),
                      ],
                    ),
                  ],
                ),
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
