import 'package:flutter/material.dart';

import '../theme/nova_colors.dart';

class NovaButton extends StatelessWidget {
  const NovaButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
    this.dark = false,
    this.fullWidth = true,
  });

  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final bool dark;
  final bool fullWidth;

  @override
  Widget build(BuildContext context) {
    final content = FilledButton.icon(
      onPressed: onPressed,
      style: FilledButton.styleFrom(
        backgroundColor: dark ? NovaColors.purple : NovaColors.gold,
        foregroundColor: dark ? NovaColors.goldLight : NovaColors.text,
        minimumSize: Size(fullWidth ? double.infinity : 0, 52),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      ),
      icon: Icon(icon ?? Icons.arrow_back_rounded, size: 18),
      label: Text(label, style: const TextStyle(fontWeight: FontWeight.w800)),
    );
    return fullWidth ? SizedBox(width: double.infinity, child: content) : content;
  }
}
