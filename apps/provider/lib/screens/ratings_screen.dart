import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/models/rating_model.dart';
import 'package:provider/service/fire_store_utils.dart';
import 'package:provider/themes/app_theme.dart';

class RatingsScreen extends StatelessWidget {
  const RatingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uid = FireStoreUtils.getCurrentUid();
    return Scaffold(
      appBar: AppBar(title: const Text('Avaliações')),
      body: StreamBuilder<List<RatingModel>>(
        stream: FireStoreUtils.watchMyReviews(uid),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting && !snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }
          final reviews = snapshot.data ?? [];
          if (reviews.isEmpty) {
            return const Center(child: Text('Nenhuma avaliação ainda.'));
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: reviews.length,
            separatorBuilder: (_, _) => const SizedBox(height: 8),
            itemBuilder: (context, i) {
              final review = reviews[i];
              final when = review.createdAt == null ? '' : DateFormat('dd/MM/yyyy').format(review.createdAt!.toDate());
              final stars = (review.rating ?? 0).round().clamp(0, 5);
              return ListTile(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: AppTheme.grey200),
                ),
                title: Text(review.uname?.isNotEmpty == true ? review.uname! : 'Cliente'),
                subtitle: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        for (var s = 1; s <= 5; s++)
                          Icon(s <= stars ? Icons.star : Icons.star_border, size: 16, color: AppTheme.warning),
                        if (when.isNotEmpty) ...[
                          const SizedBox(width: 8),
                          Text(when, style: const TextStyle(color: AppTheme.grey500, fontSize: 12)),
                        ],
                      ],
                    ),
                    if ((review.comment ?? '').isNotEmpty) ...[
                      const SizedBox(height: 6),
                      Text(review.comment!),
                    ],
                  ],
                ),
                isThreeLine: true,
              );
            },
          );
        },
      ),
    );
  }
}
