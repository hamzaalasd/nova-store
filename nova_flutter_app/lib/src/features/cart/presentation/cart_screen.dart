import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_button.dart';
import '../../../core/widgets/price_text.dart';
import '../data/cart_models.dart';
import '../data/cart_repository.dart';

class CartScreen extends ConsumerWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final cart = ref.watch(cartProvider);

    return Column(
      children: [
        const NovaAppBar(title: 'السلة'),
        Expanded(
          child: AsyncStateView(
            value: cart,
            builder: (data) {
              if (data.items.isEmpty) {
                return const _EmptyCart();
              }

              return ListView(
                padding: const EdgeInsets.fromLTRB(20, 8, 20, 24),
                children: [
                  _CartHeader(itemsCount: data.itemsCount, subtotal: data.subtotal),
                  const SizedBox(height: 16),
                  ...data.items.map((item) => _CartLine(item: item)),
                  const SizedBox(height: 16),
                  const _CouponBox(),
                  const SizedBox(height: 16),
                  _SummaryCard(cart: data),
                ],
              );
            },
          ),
        ),
      ],
    );
  }
}

class _EmptyCart extends StatelessWidget {
  const _EmptyCart();

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 118,
              height: 118,
              decoration: BoxDecoration(
                color: NovaColors.violet.withAlpha(34),
                shape: BoxShape.circle,
                border: Border.all(color: NovaColors.gold.withAlpha(80)),
              ),
              child: const Icon(Icons.shopping_bag_outlined, size: 58, color: NovaColors.gold),
            ),
            const SizedBox(height: 18),
            const Text('السلة فارغة', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
            const SizedBox(height: 8),
            const Text('ابدأ بإضافة منتجاتك المفضلة وستظهر هنا بشكل منظم.', textAlign: TextAlign.center),
            const SizedBox(height: 18),
            NovaButton(label: 'تصفح المنتجات', icon: Icons.grid_view_outlined, onPressed: () => context.push('/products')),
          ],
        ),
      ),
    );
  }
}

class _CartHeader extends StatelessWidget {
  const _CartHeader({required this.itemsCount, required this.subtotal});

  final int itemsCount;
  final double subtotal;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        gradient: const LinearGradient(colors: [NovaColors.deepNight, NovaColors.purple]),
      ),
      child: Row(
        children: [
          Container(
            width: 54,
            height: 54,
            decoration: BoxDecoration(color: NovaColors.gold, borderRadius: BorderRadius.circular(17)),
            child: const Icon(Icons.local_mall_outlined, color: NovaColors.deepNight),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('$itemsCount منتج في السلة', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 18)),
                const SizedBox(height: 4),
                const Text('راجع المنتجات قبل إكمال الشراء', style: TextStyle(color: Colors.white70)),
              ],
            ),
          ),
          PriceText(subtotal),
        ],
      ),
    );
  }
}

class _CartLine extends ConsumerWidget {
  const _CartLine({required this.item});

  final CartItem item;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [
            Container(
              width: 70,
              height: 70,
              decoration: BoxDecoration(
                color: isDark ? NovaColors.darkPurple : NovaColors.cream2,
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: isDark ? NovaColors.darkBorder : NovaColors.border),
              ),
              child: Center(
                child: Text(
                  item.product.name.characters.take(2).toString(),
                  style: const TextStyle(fontWeight: FontWeight.w900, color: NovaColors.violet),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(item.product.name, maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w900)),
                  const SizedBox(height: 5),
                  Text(item.product.sku, style: const TextStyle(color: NovaColors.muted, fontSize: 12)),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      _QuantityButton(
                        icon: Icons.remove,
                        onTap: item.quantity <= 1
                            ? null
                            : () => ref.read(cartProvider.notifier).updateQuantity(item.id, item.quantity - 1),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 11),
                        child: Text('${item.quantity}', style: const TextStyle(fontWeight: FontWeight.w900)),
                      ),
                      _QuantityButton(
                        icon: Icons.add,
                        onTap: () => ref.read(cartProvider.notifier).updateQuantity(item.id, item.quantity + 1),
                      ),
                      const Spacer(),
                      PriceText(item.lineTotal),
                    ],
                  ),
                ],
              ),
            ),
            IconButton(
              tooltip: 'حذف',
              onPressed: () => ref.read(cartProvider.notifier).remove(item.id),
              icon: const Icon(Icons.delete_outline, color: NovaColors.danger),
            ),
          ],
        ),
      ),
    );
  }
}

class _QuantityButton extends StatelessWidget {
  const _QuantityButton({required this.icon, required this.onTap});

  final IconData icon;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        width: 34,
        height: 34,
        decoration: BoxDecoration(
          color: onTap == null ? NovaColors.border.withAlpha(75) : NovaColors.violet,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Icon(icon, size: 18, color: onTap == null ? NovaColors.muted : Colors.white),
      ),
    );
  }
}

class _CouponBox extends StatelessWidget {
  const _CouponBox();

  @override
  Widget build(BuildContext context) {
    return TextField(
      decoration: InputDecoration(
        prefixIcon: const Icon(Icons.confirmation_number_outlined),
        hintText: 'كود الخصم',
        suffixIcon: Padding(
          padding: const EdgeInsets.all(6),
          child: FilledButton(
            onPressed: () {},
            style: FilledButton.styleFrom(backgroundColor: NovaColors.gold, foregroundColor: NovaColors.deepNight),
            child: const Text('تطبيق'),
          ),
        ),
      ),
    );
  }
}

class _SummaryCard extends StatelessWidget {
  const _SummaryCard({required this.cart});

  final Cart cart;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          children: [
            const Row(
              children: [
                Icon(Icons.receipt_long_outlined, color: NovaColors.gold),
                SizedBox(width: 8),
                Text('ملخص الطلب', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
              ],
            ),
            const SizedBox(height: 16),
            _SummaryRow(label: 'المجموع الفرعي', value: PriceText(cart.subtotal)),
            const SizedBox(height: 10),
            const _SummaryRow(label: 'الشحن', value: Text('يحسب عند الدفع', style: TextStyle(color: NovaColors.muted, fontWeight: FontWeight.w800))),
            const Divider(height: 26),
            _SummaryRow(label: 'الإجمالي', value: PriceText(cart.subtotal, large: true)),
            const SizedBox(height: 18),
            NovaButton(label: 'إكمال الشراء', icon: Icons.lock_outline, onPressed: () => context.push('/checkout'), dark: true),
          ],
        ),
      ),
    );
  }
}

class _SummaryRow extends StatelessWidget {
  const _SummaryRow({required this.label, required this.value});

  final String label;
  final Widget value;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(child: Text(label, style: const TextStyle(fontWeight: FontWeight.w800))),
        value,
      ],
    );
  }
}
