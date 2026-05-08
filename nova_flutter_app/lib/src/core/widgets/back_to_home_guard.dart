import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';

import '../theme/nova_colors.dart';

class BackToHomeGuard extends StatelessWidget {
  const BackToHomeGuard({
    super.key,
    required this.child,
    this.currentLocation,
  });

  final Widget child;
  final String? currentLocation;

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;

        if (Navigator.of(context).canPop()) {
          Navigator.of(context).pop();
          return;
        }

        final location = currentLocation ?? GoRouterState.of(context).uri.path;
        if (location != '/') {
          context.go('/');
          return;
        }

        final shouldExit = await showDialog<bool>(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text('الخروج من NOVA؟'),
            content: const Text('هل تريد الخروج من التطبيق فعلا؟'),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(false),
                child: const Text('إلغاء'),
              ),
              FilledButton(
                onPressed: () => Navigator.of(context).pop(true),
                style: FilledButton.styleFrom(
                  backgroundColor: NovaColors.purple,
                  foregroundColor: NovaColors.goldLight,
                ),
                child: const Text('خروج'),
              ),
            ],
          ),
        );

        if (shouldExit == true) {
          await SystemNavigator.pop();
        }
      },
      child: child,
    );
  }
}
