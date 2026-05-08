import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_button.dart';
import '../../../core/widgets/price_text.dart';
import '../../auth/data/auth_repository.dart';
import '../data/order_models.dart';
import '../data/order_repository.dart';

class OrdersScreen extends ConsumerWidget {
  const OrdersScreen({super.key});

  Future<void> _refresh(WidgetRef ref) async {
    ref.invalidate(ordersProvider);
    await ref.read(ordersProvider.future);
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final user = ref.watch(authStateProvider).asData?.value;
    if (user == null) {
      return Column(
        children: [
          const NovaAppBar(title: 'طلباتي'),
          Expanded(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: NovaButton(label: 'سجل الدخول لعرض الطلبات', onPressed: () => context.push('/login')),
              ),
            ),
          ),
        ],
      );
    }

    final orders = ref.watch(ordersProvider);
    return Column(
      children: [
        const NovaAppBar(title: 'طلباتي'),
        Expanded(
          child: RefreshIndicator(
            onRefresh: () => _refresh(ref),
            color: NovaColors.gold,
            child: AsyncStateView(
              value: orders,
              builder: (items) {
                if (items.isEmpty) {
                  return const _EmptyOrders();
                }
                return ListView.separated(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(20, 10, 20, 24),
                  itemCount: items.length,
                  separatorBuilder: (context, index) => const SizedBox(height: 14),
                  itemBuilder: (context, index) => _OrderCard(order: items[index]),
                );
              },
            ),
          ),
        ),
      ],
    );
  }
}

class _OrderCard extends StatelessWidget {
  const _OrderCard({required this.order});

  final NovaOrder order;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return InkWell(
      onTap: () => context.push('/orders/${order.id}'),
      borderRadius: BorderRadius.circular(18),
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: isDark ? NovaColors.darkSurface : Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: isDark ? NovaColors.darkBorder : NovaColors.border),
          boxShadow: [
            BoxShadow(color: Colors.black.withAlpha(isDark ? 28 : 10), blurRadius: 16, offset: const Offset(0, 8)),
          ],
        ),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(order.number, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
                  const SizedBox(height: 8),
                  Text(
                    '${order.orderStatusLabel} · ${order.paymentStatusLabel}',
                    style: const TextStyle(color: NovaColors.muted, fontWeight: FontWeight.w700),
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                PriceText(order.total),
                const SizedBox(height: 8),
                const Icon(Icons.chevron_left, color: NovaColors.gold),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _EmptyOrders extends StatelessWidget {
  const _EmptyOrders();

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(24),
      children: [
        SizedBox(height: MediaQuery.sizeOf(context).height * .22),
        const Icon(Icons.receipt_long_outlined, size: 64, color: NovaColors.gold),
        const SizedBox(height: 14),
        const Text('لا توجد طلبات بعد', textAlign: TextAlign.center, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900)),
        const SizedBox(height: 8),
        const Text('بعد إكمال أول عملية شراء ستظهر الطلبات هنا مع التتبع.', textAlign: TextAlign.center),
      ],
    );
  }
}
