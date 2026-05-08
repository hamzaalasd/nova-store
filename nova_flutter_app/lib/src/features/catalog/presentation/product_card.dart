import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/nova_network_image.dart';
import '../../../core/widgets/price_text.dart';
import '../../cart/data/cart_repository.dart';
import '../data/catalog_models.dart';

class ProductCard extends ConsumerWidget {
  const ProductCard({super.key, required this.product});

  final Product product;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return InkWell(
      borderRadius: BorderRadius.circular(18),
      onTap: () => context.push('/products/${product.slug}'),
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Stack(
                children: [
                  NovaNetworkImage(
                    path: product.mainImage,
                    fallbackText: product.name,
                  ),
                  if (product.discountPercent != null)
                    Positioned(
                      top: 10,
                      right: 10,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(color: NovaColors.gold, borderRadius: BorderRadius.circular(8)),
                        child: Text('-${product.discountPercent}%', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
                      ),
                    ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(product.category?.name ?? 'NOVA', style: const TextStyle(fontSize: 11, color: NovaColors.muted)),
                  const SizedBox(height: 4),
                  Text(
                    product.name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontWeight: FontWeight.w900, height: 1.35),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(child: PriceText(product.price)),
                      IconButton.filled(
                        onPressed: () => ref.read(cartProvider.notifier).add(product.id),
                        style: IconButton.styleFrom(backgroundColor: NovaColors.purple, foregroundColor: NovaColors.goldLight),
                        icon: const Icon(Icons.add),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
