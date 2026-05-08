import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/settings/app_preferences.dart';
import '../../../core/settings/currency_repository.dart';
import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/async_state_view.dart';
import '../../../core/widgets/nova_app_bar.dart';
import '../../../core/widgets/nova_button.dart';
import '../../auth/data/auth_repository.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider);
    final preferences = ref.watch(appPreferencesProvider).asData?.value;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Column(
      children: [
        const NovaAppBar(title: 'حسابي'),
        Expanded(
          child: AsyncStateView(
            value: auth,
            builder: (user) {
              return ListView(
                padding: const EdgeInsets.all(20),
                children: [
                  _ProfileHero(
                    name: user?.name ?? 'زائر NOVA',
                    email: user?.email ?? 'يمكنك التسوق الآن وتسجيل الدخول لاحقا',
                    isGuest: user == null,
                  ),
                  const SizedBox(height: 16),
                  _SettingsCard(
                    preferences: preferences ?? const AppPreferences(themeMode: ThemeMode.system, currencyCode: 'SAR'),
                  ),
                  const SizedBox(height: 16),
                  _ActionTile(
                    icon: Icons.receipt_long_outlined,
                    title: 'طلباتي',
                    subtitle: user == null ? 'سجل الدخول لعرض سجل الطلبات' : 'تابع حالة مشترياتك',
                    onTap: () => user == null ? context.push('/login') : context.push('/orders'),
                  ),
                  const SizedBox(height: 10),
                  _ActionTile(
                    icon: Icons.location_on_outlined,
                    title: 'العناوين',
                    subtitle: 'قريبا: إدارة عناوين الشحن',
                    onTap: () {},
                  ),
                  const SizedBox(height: 10),
                  _ActionTile(
                    icon: Icons.support_agent_outlined,
                    title: 'الدعم',
                    subtitle: 'قريبا: محادثة ومركز مساعدة',
                    onTap: () {},
                  ),
                  const SizedBox(height: 22),
                  if (user == null)
                    NovaButton(label: 'تسجيل الدخول', icon: Icons.login, onPressed: () => context.push('/login'))
                  else
                    NovaButton(
                      label: 'تسجيل الخروج',
                      icon: Icons.logout,
                      dark: !isDark,
                      onPressed: () async {
                        await ref.read(authStateProvider.notifier).logout();
                        if (context.mounted) context.go('/');
                      },
                    ),
                ],
              );
            },
          ),
        ),
      ],
    );
  }
}

class _ProfileHero extends StatelessWidget {
  const _ProfileHero({
    required this.name,
    required this.email,
    required this.isGuest,
  });

  final String name;
  final String email;
  final bool isGuest;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        gradient: const LinearGradient(colors: [NovaColors.violet, NovaColors.purple]),
      ),
      child: Row(
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              color: NovaColors.cream,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Icon(isGuest ? Icons.storefront_outlined : Icons.person, color: NovaColors.purple, size: 34),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(name, style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
                const SizedBox(height: 6),
                Text(email, style: const TextStyle(color: Colors.white70, height: 1.5, fontWeight: FontWeight.w600)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _SettingsCard extends ConsumerWidget {
  const _SettingsCard({required this.preferences});

  final AppPreferences preferences;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currencies = ref.watch(currenciesProvider);
    final selectedCurrency = ref.watch(selectedCurrencyProvider);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('إعدادات التجربة', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
            const SizedBox(height: 14),
            SegmentedButton<ThemeMode>(
              segments: const [
                ButtonSegment(value: ThemeMode.light, icon: Icon(Icons.light_mode_outlined), label: Text('فاتح')),
                ButtonSegment(value: ThemeMode.dark, icon: Icon(Icons.dark_mode_outlined), label: Text('ليلي')),
                ButtonSegment(value: ThemeMode.system, icon: Icon(Icons.phone_android), label: Text('النظام')),
              ],
              selected: {preferences.themeMode},
              onSelectionChanged: (selection) => ref.read(appPreferencesProvider.notifier).setThemeMode(selection.first),
            ),
            const SizedBox(height: 14),
            currencies.when(
              data: (items) => DropdownButtonFormField<String>(
                initialValue: selectedCurrency.code,
                decoration: InputDecoration(
                  labelText: 'العملة',
                  prefixIcon: const Icon(Icons.payments_outlined),
                  suffixIcon: IconButton(
                    tooltip: 'تحديث العملات',
                    onPressed: () => ref.invalidate(currenciesProvider),
                    icon: const Icon(Icons.refresh),
                  ),
                ),
                items: items
                    .map(
                      (currency) => DropdownMenuItem(
                        value: currency.code,
                        child: Text('${currency.name} (${currency.code})'),
                      ),
                    )
                    .toList(),
                onChanged: (value) {
                  if (value != null) ref.read(appPreferencesProvider.notifier).setCurrencyCode(value);
                },
              ),
              loading: () => const LinearProgressIndicator(color: NovaColors.gold),
              error: (error, stackTrace) => Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('تعذر جلب العملات من الخادم.', style: TextStyle(color: NovaColors.danger, fontWeight: FontWeight.w800)),
                  TextButton.icon(
                    onPressed: () => ref.invalidate(currenciesProvider),
                    icon: const Icon(Icons.refresh),
                    label: const Text('إعادة المحاولة'),
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

class _ActionTile extends StatelessWidget {
  const _ActionTile({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      tileColor: Theme.of(context).cardTheme.color,
      leading: Icon(icon, color: NovaColors.gold),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
      subtitle: Text(subtitle),
      trailing: const Icon(Icons.chevron_left),
    );
  }
}
