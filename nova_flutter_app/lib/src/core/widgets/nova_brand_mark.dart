import 'package:flutter/material.dart';

import '../theme/nova_colors.dart';

class NovaBrandMark extends StatelessWidget {
  const NovaBrandMark({
    super.key,
    this.size = 74,
    this.borderRadius,
  });

  final double size;
  final BorderRadius? borderRadius;

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: borderRadius ?? BorderRadius.circular(size * .23),
      child: CustomPaint(
        size: Size.square(size),
        painter: const _NovaMarkPainter(),
      ),
    );
  }
}

class _NovaMarkPainter extends CustomPainter {
  const _NovaMarkPainter();

  @override
  void paint(Canvas canvas, Size size) {
    final scale = size.width / 120;
    Offset p(double x, double y) => Offset(x * scale, y * scale);
    double s(double value) => value * scale;

    final bg = Paint()..color = const Color(0xFF2D2438);
    canvas.drawRect(Offset.zero & size, bg);

    final gold = Paint()..color = const Color(0xFFD4AF7A);
    final warmGold = Paint()..color = const Color(0xFFB8965A);
    final cream = Paint()..color = NovaColors.cream.withAlpha(230);

    final nPath = Path()
      ..moveTo(p(28, 88).dx, p(28, 88).dy)
      ..lineTo(p(28, 32).dx, p(28, 32).dy)
      ..lineTo(p(44, 32).dx, p(44, 32).dy)
      ..lineTo(p(60, 66).dx, p(60, 66).dy)
      ..lineTo(p(60, 32).dx, p(60, 32).dy)
      ..lineTo(p(76, 32).dx, p(76, 32).dy)
      ..lineTo(p(76, 52).dx, p(76, 52).dy)
      ..lineTo(p(60, 52).dx, p(60, 52).dy)
      ..lineTo(p(60, 66).dx, p(60, 66).dy)
      ..lineTo(p(76, 88).dx, p(76, 88).dy)
      ..lineTo(p(60, 88).dx, p(60, 88).dy)
      ..lineTo(p(44, 54).dx, p(44, 54).dy)
      ..lineTo(p(44, 88).dx, p(44, 88).dy)
      ..close();
    canvas.drawPath(nPath, gold);

    canvas.drawRect(Rect.fromLTWH(s(76), s(32), s(16), s(56)), warmGold);

    final topPath = Path()
      ..moveTo(p(76, 32).dx, p(76, 32).dy)
      ..lineTo(p(92, 32).dx, p(92, 32).dy)
      ..lineTo(p(92, 52).dx, p(92, 52).dy)
      ..lineTo(p(76, 52).dx, p(76, 52).dy)
      ..close();
    canvas.drawPath(topPath, gold);

    canvas.drawCircle(p(88, 34), s(5), cream);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
