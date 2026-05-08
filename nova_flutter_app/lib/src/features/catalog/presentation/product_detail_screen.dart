import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_button.dart';
import '../../../core/widgets/nova_network_image.dart';
import '../../../core/widgets/price_text.dart';
import '../../cart/data/cart_repository.dart';
import '../data/catalog_repository.dart';

class ProductDetailScreen extends ConsumerWidget {
  const ProductDetailScreen({super.key, required this.productSlug});

  final String productSlug;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final product = ref.watch(productProvider(productSlug));

    return AsyncStateView(
      value: product,
      builder: (item) => ListView(
        padding: const EdgeInsets.all(20),
        children: [
          AspectRatio(
            aspectRatio: 1.05,
            child: NovaNetworkImage(
              path: item.mainImage,
              fallbackText: item.name,
              borderRadius: BorderRadius.circular(24),
            ),
          ),
          const SizedBox(height: 18),
          Text(item.category?.name ?? 'NOVA', style: const TextStyle(color: NovaColors.gold, fontWeight: FontWeight.w900)),
          const SizedBox(height: 8),
          Text(item.name, style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w900, height: 1.25)),
          const SizedBox(height: 10),
          Text(item.description ?? item.shortDescription ?? 'تفاصيل المنتج ستظهر من Laravel API.', style: const TextStyle(color: NovaColors.muted, height: 1.8)),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: NovaColors.cream2, borderRadius: BorderRadius.circular(18)),
            child: Row(
              children: [
                Expanded(child: PriceText(item.price, large: true)),
                if (item.discountPercent != null)
                  Chip(label: Text('خصم ${item.discountPercent}%'), backgroundColor: NovaColors.gold),
              ],
            ),
          ),
          const SizedBox(height: 20),
          NovaButton(
            label: 'إضافة للسلة',
            dark: true,
            icon: Icons.shopping_bag_outlined,
            onPressed: () => ref.read(cartProvider.notifier).add(item.id),
          ),
        ],
      ),
    );
  }
}
