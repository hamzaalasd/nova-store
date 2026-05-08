import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_brand_mark.dart';
import '../../../core/widgets/price_text.dart';
import '../data/order_models.dart';
import '../data/order_repository.dart';

class OrderTrackingScreen extends ConsumerWidget {
  const OrderTrackingScreen({super.key, required this.orderId});

  final int orderId;

  Future<void> _refresh(WidgetRef ref) async {
    ref.invalidate(orderProvider(orderId));
    ref.invalidate(ordersProvider);
    await ref.read(orderProvider(orderId).future);
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final order = ref.watch(orderProvider(orderId));

    return Scaffold(
      backgroundColor: NovaColors.deepNight,
      body: SafeArea(
        child: Column(
          children: [
            NovaAppBar(
              title: 'تتبع الطلب',
              actions: [
                IconButton(onPressed: () => context.go('/orders'), icon: const Icon(Icons.close)),
              ],
            ),
            Expanded(
              child: RefreshIndicator(
                onRefresh: () => _refresh(ref),
                color: NovaColors.gold,
                child: AsyncStateView(
                  value: order,
                  builder: (item) => ListView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.fromLTRB(22, 10, 22, 28),
                    children: [
                      const _TrackingHeader(),
                      const SizedBox(height: 36),
                      _TrackingHero(order: item),
                      const SizedBox(height: 34),
                      _OrderSummary(order: item),
                      const SizedBox(height: 16),
                      _OrderItems(order: item),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _TrackingHeader extends StatelessWidget {
  const _TrackingHeader();

  @override
  Widget build(BuildContext context) {
    return const Row(
      children: [
        NovaBrandMark(size: 42),
        SizedBox(width: 12),
        Text.rich(
          TextSpan(text: 'Nova ', children: [TextSpan(text: 'Store', style: TextStyle(color: NovaColors.gold))]),
          style: TextStyle(color: NovaColors.cream, fontSize: 24, fontWeight: FontWeight.w900),
        ),
      ],
    );
  }
}

class _TrackingHero extends StatelessWidget {
  const _TrackingHero({required this.order});

  final NovaOrder order;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        SizedBox(
          height: 250,
          child: Stack(
            alignment: Alignment.center,
            children: [
              Container(
                width: 235,
                height: 235,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: NovaColors.violet.withAlpha(70), width: 2),
                ),
              ),
              _TrackingLine(step: order.trackingStep, cancelled: order.isCancelled),
            ],
          ),
        ),
        const SizedBox(height: 26),
        Text(
          order.isCancelled ? 'تم إيقاف معالجة الطلب' : 'تتبع طلبك لحظة بلحظة',
          textAlign: TextAlign.center,
          style: const TextStyle(color: NovaColors.cream, fontSize: 30, fontWeight: FontWeight.w900, height: 1.25),
        ),
        const SizedBox(height: 12),
        Text(
          '${order.number} · ${order.orderStatusLabel} · ${order.paymentStatusLabel}',
          textAlign: TextAlign.center,
          style: const TextStyle(color: NovaColors.muted, fontSize: 16, height: 1.7, fontWeight: FontWeight.w700),
        ),
      ],
    );
  }
}

class _TrackingLine extends StatelessWidget {
  const _TrackingLine({required this.step, required this.cancelled});

  final int step;
  final bool cancelled;

  static const _steps = [
    _StepData(label: 'تأكيد', icon: Icons.check),
    _StepData(label: 'تجهيز', icon: Icons.inventory_2_outlined),
    _StepData(label: 'توصيل', icon: Icons.local_shipping_outlined),
    _StepData(label: 'وصل', icon: Icons.home_outlined),
  ];

  @override
  Widget build(BuildContext context) {
    final activeStep = cancelled ? -1 : step;
    return SizedBox(
      width: 300,
      height: 118,
      child: Stack(
        alignment: Alignment.center,
        children: [
          Positioned(
            top: 44,
            left: 44,
            right: 44,
            child: Container(height: 5, color: NovaColors.violet.withAlpha(80)),
          ),
          Positioned(
            top: 44,
            right: 44,
            width: activeStep <= 0 ? 0 : (212 / 3) * activeStep,
            child: Container(height: 5, color: NovaColors.gold),
          ),
          for (var i = 0; i < _steps.length; i++)
            Positioned(
              right: 22.0 + (i * 74),
              top: i == activeStep ? 0 : 18,
              child: _TrackingStep(data: _steps[i], active: i == activeStep, done: i < activeStep),
            ),
        ],
      ),
    );
  }
}

class _TrackingStep extends StatelessWidget {
  const _TrackingStep({required this.data, required this.active, required this.done});

  final _StepData data;
  final bool active;
  final bool done;

  @override
  Widget build(BuildContext context) {
    final color = active ? NovaColors.gold : (done ? NovaColors.violet : NovaColors.darkPurple);
    final foreground = active ? NovaColors.deepNight : NovaColors.cream;
    return Column(
      children: [
        AnimatedContainer(
          duration: const Duration(milliseconds: 260),
          curve: Curves.easeOutBack,
          width: active ? 76 : 52,
          height: active ? 76 : 52,
          decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(active ? 22 : 999)),
          child: Icon(data.icon, color: foreground, size: active ? 34 : 25),
        ),
        const SizedBox(height: 10),
        Text(
          data.label,
          style: TextStyle(color: active ? NovaColors.gold : NovaColors.muted, fontWeight: FontWeight.w900, fontSize: active ? 15 : 13),
        ),
      ],
    );
  }
}

class _StepData {
  const _StepData({required this.label, required this.icon});

  final String label;
  final IconData icon;
}

class _OrderSummary extends StatelessWidget {
  const _OrderSummary({required this.order});

  final NovaOrder order;

  @override
  Widget build(BuildContext context) {
    return _DarkCard(
      child: Column(
        children: [
          _SummaryRow(label: 'رقم الطلب', value: Text(order.number, style: const TextStyle(color: NovaColors.cream, fontWeight: FontWeight.w900))),
          const SizedBox(height: 12),
          _SummaryRow(label: 'حالة الطلب', value: Text(order.orderStatusLabel, style: const TextStyle(color: NovaColors.gold, fontWeight: FontWeight.w900))),
          const SizedBox(height: 12),
          _SummaryRow(label: 'حالة الدفع', value: Text(order.paymentStatusLabel, style: const TextStyle(color: NovaColors.darkMuted, fontWeight: FontWeight.w900))),
          const Divider(height: 28, color: NovaColors.darkBorder),
          _SummaryRow(label: 'الإجمالي', value: PriceText(order.total, large: true)),
        ],
      ),
    );
  }
}

class _OrderItems extends StatelessWidget {
  const _OrderItems({required this.order});

  final NovaOrder order;

  @override
  Widget build(BuildContext context) {
    if (order.items.isEmpty) return const SizedBox.shrink();
    return _DarkCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('منتجات الطلب', style: TextStyle(color: NovaColors.cream, fontSize: 18, fontWeight: FontWeight.w900)),
          const SizedBox(height: 12),
          for (final item in order.items) ...[
            Row(
              children: [
                Expanded(
                  child: Text(
                    '${item.name} × ${item.quantity}',
                    style: const TextStyle(color: NovaColors.darkMuted, fontWeight: FontWeight.w800),
                  ),
                ),
                PriceText(item.total),
              ],
            ),
            const SizedBox(height: 10),
          ],
        ],
      ),
    );
  }
}

class _DarkCard extends StatelessWidget {
  const _DarkCard({required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: NovaColors.darkSurface,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: NovaColors.darkBorder),
      ),
      child: child,
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
        Expanded(child: Text(label, style: const TextStyle(color: NovaColors.muted, fontWeight: FontWeight.w800))),
        value,
      ],
    );
  }
}
