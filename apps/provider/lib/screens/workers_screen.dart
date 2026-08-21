import 'package:arrow_shared/rating_average.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/models/worker_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class WorkersScreen extends StatelessWidget {
  const WorkersScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Equipe')),
      body: StreamBuilder<List<WorkerModel>>(
        stream: FireStoreUtils.watchMyWorkers(uid),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }
          final workers = snapshot.data ?? [];
          if (workers.isEmpty) {
            return const Center(child: Text('Nenhum profissional em providers_workers.'));
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: workers.length,
            separatorBuilder: (_, _) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final worker = workers[i];
              return SwitchListTile(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: AppTheme.grey200),
                ),
                secondary: CircleAvatar(
                  backgroundImage: worker.profilePictureURL.isNotEmpty ? CachedNetworkImageProvider(worker.profilePictureURL) : null,
                  child: worker.profilePictureURL.isEmpty ? Text(worker.firstName.isNotEmpty ? worker.firstName[0] : '?') : null,
                ),
                title: Text(worker.fullName()),
                subtitle: Text([
                  if (worker.email.isNotEmpty) worker.email,
                  if (worker.salary.isNotEmpty) 'Salário: ${worker.salary}',
                  worker.online ? 'Online' : 'Offline',
                  if (worker.reviewsCount > 0)
                    '${RatingAverage.formatted(worker.reviewsSum, worker.reviewsCount)} (${worker.reviewsCount} avaliações)',
                ].join(' · ')),
                value: worker.online,
                onChanged: (value) => FireStoreUtils.setWorkerOnline(worker.id, value),
              );
            },
          );
        },
      ),
    );
  }
}
