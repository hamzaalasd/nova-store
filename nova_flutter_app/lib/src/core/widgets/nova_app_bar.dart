import 'package:flutter/material.dart';

import '../theme/nova_colors.dart';

class NovaAppBar extends StatelessWidget implements PreferredSizeWidget {
  const NovaAppBar({
    super.key,
    this.title = 'NOVA',
    this.actions = const [],
  });

  final String title;
  final List<Widget> actions;

  @override
  Size get preferredSize => const Size.fromHeight(68);

  @override
  Widget build(BuildContext context) {
    return AppBar(
      titleSpacing: 20,
      title: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
          const Text(
            'Nova Signature',
            style: TextStyle(fontSize: 11, color: NovaColors.muted, fontWeight: FontWeight.w600),
          ),
        ],
      ),
      actions: actions,
    );
  }
}
