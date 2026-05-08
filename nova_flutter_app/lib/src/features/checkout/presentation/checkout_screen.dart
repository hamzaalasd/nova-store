import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/network/api_client.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_button.dart';
import '../../../core/widgets/price_text.dart';
import '../../auth/data/auth_repository.dart';
import '../../cart/data/cart_repository.dart';
import '../../orders/data/order_repository.dart';

class CheckoutScreen extends ConsumerStatefulWidget {
  const CheckoutScreen({super.key});

  @override
  ConsumerState<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends ConsumerState<CheckoutScreen> {
  final _city = TextEditingController(text: 'Riyadh');
  final _district = TextEditingController(text: 'Al Olaya');
  final _street = TextEditingController(text: 'King Fahd Road');
  bool _submitting = false;

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authStateProvider).asData?.value;
    final cart = ref.watch(cartProvider);

    if (auth == null) {
      return Column(
        children: [
          const NovaAppBar(title: 'الدفع'),
          Expanded(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: NovaButton(label: 'سجل الدخول لإكمال الشراء', onPressed: () => context.push('/login')),
              ),
            ),
          ),
        ],
      );
    }

    return Column(
      children: [
        const NovaAppBar(title: 'إكمال الشراء'),
        Expanded(
          child: AsyncStateView(
            value: cart,
            builder: (data) => ListView(
              padding: const EdgeInsets.all(20),
              children: [
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        TextField(controller: _city, decoration: const InputDecoration(labelText: 'المدينة')),
                        const SizedBox(height: 12),
                        TextField(controller: _district, decoration: const InputDecoration(labelText: 'الحي')),
                        const SizedBox(height: 12),
                        TextField(controller: _street, decoration: const InputDecoration(labelText: 'الشارع')),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Expanded(child: Text('الإجمالي', style: TextStyle(fontWeight: FontWeight.w900))),
                        PriceText(data.subtotal, large: true),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                NovaButton(
                  label: _submitting ? 'جاري إنشاء الطلب...' : 'تأكيد الطلب',
                  onPressed: _submitting
                      ? null
                      : () async {
                          setState(() => _submitting = true);
                          try {
                            final order = await ref.read(orderRepositoryProvider).create(
                                  name: auth.name,
                                  email: auth.email,
                                  phone: auth.phone ?? '',
                                  city: _city.text,
                                  district: _district.text,
                                  street: _street.text,
                                );
                            ref.invalidate(cartProvider);
                            ref.invalidate(ordersProvider);
                            if (context.mounted) context.go('/orders/${order.id}');
                          } catch (error) {
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(readableApiError(error))));
                            }
                          } finally {
                            if (mounted) setState(() => _submitting = false);
                          }
                        },
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
