import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../config/app_config.dart';
import '../theme/nova_colors.dart';

class NovaNetworkImage extends StatelessWidget {
  const NovaNetworkImage({
    super.key,
    required this.path,
    required this.fallbackText,
    this.borderRadius,
  });

  final String? path;
  final String fallbackText;
  final BorderRadius? borderRadius;

  @override
  Widget build(BuildContext context) {
    final url = imageUrl(path);
    final fallback = _ImageFallback(text: fallbackText);

    final child = url == null
        ? fallback
        : CachedNetworkImage(
            imageUrl: url,
            fit: BoxFit.cover,
            width: double.infinity,
            height: double.infinity,
            placeholder: (context, url) => const Center(
              child: CircularProgressIndicator(color: NovaColors.gold, strokeWidth: 2),
            ),
            errorWidget: (context, url, error) => fallback,
          );

    return ClipRRect(
      borderRadius: borderRadius ?? BorderRadius.zero,
      child: child,
    );
  }

  static String? imageUrl(String? rawPath) {
    final value = rawPath?.trim();
    if (value == null || value.isEmpty || value == 'null') return null;
    if (value.startsWith('http://') || value.startsWith('https://')) return value;
    return '${AppConfig.storageBaseUrl}/${value.replaceFirst(RegExp('^/+'), '')}';
  }
}

class _ImageFallback extends StatelessWidget {
  const _ImageFallback({required this.text});

  final String text;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      width: double.infinity,
      height: double.infinity,
      color: isDark ? NovaColors.darkPurple : NovaColors.cream2,
      child: Center(
        child: Text(
          text.characters.take(2).toString(),
          style: const TextStyle(fontSize: 30, fontWeight: FontWeight.w900, color: NovaColors.violet),
        ),
      ),
    );
  }
}
