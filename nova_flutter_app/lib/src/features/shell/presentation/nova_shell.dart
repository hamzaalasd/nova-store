import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../core/theme/nova_colors.dart';
import '../../../core/widgets/back_to_home_guard.dart';

class NovaShell extends StatelessWidget {
  const NovaShell({super.key, required this.child});

  final Widget child;

  static const _items = [
    _NavItem(path: '/', label: 'الرئيسية', icon: Icons.home_outlined, activeIcon: Icons.home_rounded),
    _NavItem(path: '/products', label: 'المتجر', icon: Icons.grid_view_outlined, activeIcon: Icons.grid_view_rounded),
    _NavItem(path: '/cart', label: 'السلة', icon: Icons.shopping_bag_outlined, activeIcon: Icons.shopping_bag_rounded),
    _NavItem(path: '/orders', label: 'طلباتي', icon: Icons.receipt_long_outlined, activeIcon: Icons.receipt_long_rounded),
    _NavItem(path: '/profile', label: 'حسابي', icon: Icons.person_outline_rounded, activeIcon: Icons.person_rounded),
  ];

  int _indexFor(String location) {
    if (location.startsWith('/products')) return 1;
    if (location.startsWith('/cart') || location.startsWith('/checkout')) return 2;
    if (location.startsWith('/orders')) return 3;
    if (location.startsWith('/profile')) return 4;
    return 0;
  }

  @override
  Widget build(BuildContext context) {
    final location = GoRouterState.of(context).uri.toString();
    final path = Uri.parse(location).path;
    final index = _indexFor(path);

    return BackToHomeGuard(
      currentLocation: path,
      child: Scaffold(
        extendBody: true,
        body: SafeArea(child: child),
        bottomNavigationBar: _NovaBottomNav(
          items: _items,
          selectedIndex: index,
          onTap: (value) => context.go(_items[value].path),
        ),
      ),
    );
  }
}

class _NovaBottomNav extends StatelessWidget {
  const _NovaBottomNav({
    required this.items,
    required this.selectedIndex,
    required this.onTap,
  });

  final List<_NavItem> items;
  final int selectedIndex;
  final ValueChanged<int> onTap;

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.paddingOf(context).bottom;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Padding(
      padding: EdgeInsets.fromLTRB(10, 0, 10, bottomInset > 0 ? 8 : 12),
      child: DecoratedBox(
        decoration: BoxDecoration(
          color: isDark ? NovaColors.darkPurple : Colors.white,
          borderRadius: BorderRadius.circular(28),
          border: Border.all(color: isDark ? NovaColors.darkBorder : const Color(0xFFECE8E2)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(isDark ? 65 : 22),
              blurRadius: 24,
              offset: const Offset(0, 12),
            ),
          ],
        ),
        child: SizedBox(
          height: 76,
          child: Row(
            textDirection: TextDirection.rtl,
            children: [
              for (var i = 0; i < items.length; i++)
                Expanded(
                  child: _NavButton(
                    item: items[i],
                    active: i == selectedIndex,
                    onTap: () => onTap(i),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}

class _NavButton extends StatelessWidget {
  const _NavButton({
    required this.item,
    required this.active,
    required this.onTap,
  });

  final _NavItem item;
  final bool active;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final inactiveColor = isDark ? NovaColors.darkMuted : const Color(0xFFB7A9C5);
    final activeColor = NovaColors.gold;
    final bubbleColor = isDark ? NovaColors.deepNight : NovaColors.cream;

    return Semantics(
      button: true,
      selected: active,
      label: item.label,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(24),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 7),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              AnimatedContainer(
                duration: const Duration(milliseconds: 260),
                curve: Curves.easeOutCubic,
                width: active ? 58 : 42,
                height: 36,
                decoration: BoxDecoration(
                  color: active ? bubbleColor : Colors.transparent,
                  borderRadius: BorderRadius.circular(22),
                ),
                child: AnimatedScale(
                  scale: active ? 1.05 : .96,
                  duration: const Duration(milliseconds: 220),
                  curve: Curves.easeOutBack,
                  child: Icon(
                    active ? item.activeIcon : item.icon,
                    color: active ? activeColor : inactiveColor,
                    size: active ? 24 : 22,
                  ),
                ),
              ),
              const SizedBox(height: 3),
              AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 220),
                curve: Curves.easeOut,
                style: TextStyle(
                  color: active ? activeColor : inactiveColor,
                  fontSize: active ? 12.5 : 11.5,
                  fontWeight: active ? FontWeight.w900 : FontWeight.w700,
                  height: 1.1,
                ),
                child: Text(
                  item.label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _NavItem {
  const _NavItem({
    required this.path,
    required this.label,
    required this.icon,
    required this.activeIcon,
  });

  final String path;
  final String label;
  final IconData icon;
  final IconData activeIcon;
}
